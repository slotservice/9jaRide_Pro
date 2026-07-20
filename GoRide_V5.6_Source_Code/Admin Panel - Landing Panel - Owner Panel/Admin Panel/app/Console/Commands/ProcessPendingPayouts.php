<?php

namespace App\Console\Commands;

use App\Http\Controllers\PayoutTransferController;
use Illuminate\Console\Command;
use Kreait\Firebase\Contract\Firestore;

/**
 * Automatic driver payouts.
 *
 * Runs every minute (system cron, from /var/www/admin). Finds pending driver
 * withdrawal requests and pays each one to the driver's bank via the shared
 * PayoutTransferController::processPayout() — the same code the admin approve
 * button uses.
 *
 * Safety:
 *  - Kill switch: only runs when settings/payment.payStack.autoPayoutEnabled === true.
 *  - Amount ceiling: anything above maxAutoPayout (default 100000 naira) is left
 *    for a human and flagged autoPayoutStatus = 'manual_review'.
 *  - Permanent failures (no/invalid bank, bad amount) are flagged 'manual_review'
 *    so they are not retried forever; transient ones (insufficient balance,
 *    Paystack hiccup) are left pending and retried on the next run.
 *  - Double-payment is impossible: processPayout() only touches 'pending' rows
 *    and flips them to 'approved' the instant the transfer is accepted.
 */
class ProcessPendingPayouts extends Command
{
    protected $signature = 'payouts:process-pending';

    protected $description = 'Automatically pay pending driver withdrawal requests to their banks via Paystack.';

    private const DEFAULT_MAX_AUTO_NAIRA = 100000;
    private const MAX_PER_RUN = 25;

    public function handle(): int
    {
        try {
            $db = app(Firestore::class)->database();
        } catch (\Throwable $e) {
            $this->error('firestore init failed: ' . $e->getMessage());
            return self::SUCCESS;
        }

        // Kill switch + limit live in Firestore so they can change without a deploy.
        $ps = [];
        try {
            $s = $db->collection('settings')->document('payment')->snapshot();
            $ps = ($s->exists() ? ($s->data()['payStack'] ?? []) : []) ?: [];
        } catch (\Throwable $e) {
            $this->error('settings read failed: ' . $e->getMessage());
            return self::SUCCESS;
        }

        if (($ps['autoPayoutEnabled'] ?? false) !== true) {
            return self::SUCCESS; // auto payouts are switched off
        }
        $maxAuto = floatval($ps['maxAutoPayout'] ?? self::DEFAULT_MAX_AUTO_NAIRA);

        $controller = app(PayoutTransferController::class);
        $processed = 0;

        try {
            $docs = $db->collection('withdrawal_history')->where('paymentStatus', '=', 'pending')->documents();
        } catch (\Throwable $e) {
            $this->error('pending query failed: ' . $e->getMessage());
            return self::SUCCESS;
        }

        foreach ($docs as $doc) {
            if (!$doc->exists()) {
                continue;
            }
            if ($processed >= self::MAX_PER_RUN) {
                break;
            }
            $w = $doc->data();
            $id = $doc->id();

            // Skip anything a human already parked for review or put on hold.
            $autoStatus = (string) ($w['autoPayoutStatus'] ?? '');
            if ($autoStatus === 'manual_review' || ($w['autoHold'] ?? false) === true) {
                continue;
            }

            // Amount ceiling — larger payouts wait for a human.
            $amountNaira = floatval($w['amount'] ?? 0);
            if ($amountNaira > $maxAuto) {
                $doc->reference()->set([
                    'autoPayoutStatus' => 'manual_review',
                    'adminNote' => 'Above the automatic payout limit (' . $maxAuto . ' naira) — needs manual approval.',
                ], ['merge' => true]);
                $this->warn("held $id: above limit ($amountNaira > $maxAuto)");
                continue;
            }

            $processed++;
            try {
                $r = $controller->processPayout($id);
            } catch (\Throwable $e) {
                // Unexpected error — leave pending, retry next run.
                $this->error("payout $id threw: " . $e->getMessage());
                continue;
            }

            if (!empty($r['ok'])) {
                $this->info("paid $id: " . ($r['message'] ?? ''));
            } elseif (($r['status'] ?? '') === 'otp') {
                $this->warn("payout $id blocked on OTP — turn off 'OTP for transfers' in Paystack.");
            } elseif (!empty($r['permanent'])) {
                $doc->reference()->set([
                    'autoPayoutStatus' => 'manual_review',
                    'adminNote' => (string) ($r['message'] ?? 'Automatic payout could not be completed — needs manual review.'),
                ], ['merge' => true]);
                $this->warn("flagged $id for manual review: " . ($r['message'] ?? ''));
            } else {
                // Transient (insufficient balance, Paystack hiccup) — retry next run.
                $this->warn("deferred $id: " . ($r['message'] ?? ''));
            }
        }

        if ($processed > 0) {
            $this->info("run complete: attempted $processed payout(s).");
        }
        return self::SUCCESS;
    }
}

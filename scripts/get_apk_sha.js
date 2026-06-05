// Extract SHA-1 and SHA-256 of the signing certificate from a v2/v3-signed APK.
// Usage: node get_apk_sha.js <path-to.apk>
const fs = require('fs');
const crypto = require('crypto');

const apkPath = process.argv[2];
if (!apkPath) { console.error('usage: node get_apk_sha.js <apk>'); process.exit(1); }
const buf = fs.readFileSync(apkPath);

// 1. Find End Of Central Directory (EOCD) record. Signature 0x06054b50, max 65535 bytes of comment after it.
const EOCD_SIG = 0x06054b50;
let eocdOff = -1;
for (let i = buf.length - 22; i >= Math.max(0, buf.length - 22 - 65535); i--) {
  if (buf.readUInt32LE(i) === EOCD_SIG) { eocdOff = i; break; }
}
if (eocdOff < 0) { console.error('EOCD not found'); process.exit(1); }
const centralDirOff = buf.readUInt32LE(eocdOff + 16);

// 2. APK Signing Block sits just before central dir. Layout near end of signing block:
//    [ size (8 bytes) ][ magic 16 bytes "APK Sig Block 42" ]
// The magic is at centralDirOff - 16; size-of-block at centralDirOff - 24.
const magicOff = centralDirOff - 16;
const magic = buf.slice(magicOff, magicOff + 16).toString('utf8');
if (magic !== 'APK Sig Block 42') { console.error('APK Signing Block magic not found, got:', JSON.stringify(magic)); process.exit(1); }
const sigBlockSize = Number(buf.readBigUInt64LE(centralDirOff - 24));
// The full block is [size(8) | pairs | size(8) | magic(16)]. Start of pairs:
const blockStart = centralDirOff - sigBlockSize - 8;
const pairsStart = blockStart + 8;
const pairsEnd = centralDirOff - 24;

// 3. Walk id-value pairs. Each pair: length(8 bytes, includes id and value) | id(4) | value.
const SCHEME_V2 = 0x7109871a;
const SCHEME_V3 = 0xf05368c0;
let v2 = null, v3 = null;
let p = pairsStart;
while (p < pairsEnd) {
  const pairLen = Number(buf.readBigUInt64LE(p));
  const id = buf.readUInt32LE(p + 8);
  const valStart = p + 12;
  const valEnd = p + 8 + pairLen;
  if (id === SCHEME_V2) v2 = buf.slice(valStart, valEnd);
  if (id === SCHEME_V3) v3 = buf.slice(valStart, valEnd);
  p = valEnd;
}
const scheme = v3 || v2;
if (!scheme) { console.error('No v2/v3 scheme block'); process.exit(1); }

// 4. Scheme block = sequence-of-signers (length-prefixed u32 of signers, each signer length-prefixed).
//    signer = signed-data (length-prefixed) | ...
//    signed-data = digests (lp seq) | certificates (lp seq) | ...
//    certificates = sequence of length-prefixed cert DER bytes.
function readLP32(buf, off) { const len = buf.readUInt32LE(off); return { data: buf.slice(off + 4, off + 4 + len), next: off + 4 + len }; }

// signers sequence
const signersSeq = readLP32(scheme, 0).data;
// take first signer
const firstSigner = readLP32(signersSeq, 0).data;
// signed-data is the first length-prefixed block inside the signer
const signedData = readLP32(firstSigner, 0).data;
// inside signed-data: digests (skip), then certificates
let off = 0;
const digests = readLP32(signedData, off); off = digests.next;
const certs = readLP32(signedData, off).data;
// first cert inside certs sequence
const firstCert = readLP32(certs, 0).data;

// 5. Hash the DER cert.
const sha1 = crypto.createHash('sha1').update(firstCert).digest('hex').toUpperCase();
const sha256 = crypto.createHash('sha256').update(firstCert).digest('hex').toUpperCase();
const colon = s => s.match(/.{2}/g).join(':');

console.log('APK:        ', apkPath);
console.log('SHA-1:      ', colon(sha1));
console.log('SHA-256:    ', colon(sha256));

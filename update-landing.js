const admin = require("firebase-admin");
if (!admin.apps.length) {
  const sa = require("/var/www/9jaRide_Pro/GoRide_V5.6_Source_Code/Admin Panel - Landing Panel - Owner Panel/Admin Panel/firebase.json");
  admin.initializeApp({ credential: admin.credential.cert(sa) });
}
const db = admin.firestore();
const BASE = "http://148.230.120.40:8081/img";

async function update() {

  await db.collection("settings").doc("headerTemplate").set({
    headerTemplate: '<nav style="background:#1B5E20;padding:12px 30px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 10px rgba(0,0,0,0.15);"><div style="display:flex;align-items:center;gap:10px;"><img src="' + BASE + '/9jaride-pro-logo.svg" style="height:40px;" alt="9jaRide Pro"></div><div style="display:flex;align-items:center;gap:20px;"><a href="#features" style="color:#fff;text-decoration:none;font-weight:500;font-size:15px;">Features</a><a href="#about" style="color:#fff;text-decoration:none;font-weight:500;font-size:15px;">About</a><a href="#event" style="color:#fff;text-decoration:none;font-weight:500;font-size:15px;">Events</a><a href="#contact" style="color:#fff;text-decoration:none;font-weight:500;font-size:15px;">Contact</a><a href="#" style="background:#D4AF37;color:#fff;padding:8px 20px;border-radius:25px;text-decoration:none;font-weight:600;font-size:14px;">Download App</a></div></nav>'
  });

  const landingHTML = `<div style="font-family:Poppins,sans-serif;">
    <section style="position:relative;overflow:hidden;">
      <img src="${BASE}/img2.jpeg" style="width:100%;height:auto;display:block;" alt="Welcome to 9jaRide Pro">
    </section>

    <section style="background:#0F2B15;">
      <img src="${BASE}/img1.jpeg" style="width:100%;height:auto;display:block;" alt="9jaRide Pro x OFMECH Partnership">
    </section>

    <section id="features" style="padding:60px 30px;background:#f5f7f5;">
      <h2 style="text-align:center;color:#1B5E20;margin-bottom:15px;font-size:32px;font-weight:700;">Why Choose 9jaRide Pro?</h2>
      <p style="text-align:center;color:#666;margin-bottom:40px;font-size:16px;">Your Trusted Journey Partner</p>
      <div style="display:flex;justify-content:center;gap:30px;flex-wrap:wrap;max-width:1100px;margin:0 auto;">
        <div style="background:#fff;padding:35px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);flex:1;min-width:280px;text-align:center;border-top:3px solid #D4AF37;">
          <div style="font-size:40px;margin-bottom:15px;">&#128176;</div>
          <h3 style="color:#1B5E20;margin-bottom:10px;">Competitive Bidding</h3>
          <p style="color:#666;line-height:1.6;">Drivers bid on your ride request. You choose the best price and highest-rated driver.</p>
        </div>
        <div style="background:#fff;padding:35px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);flex:1;min-width:280px;text-align:center;border-top:3px solid #D4AF37;">
          <div style="font-size:40px;margin-bottom:15px;">&#128205;</div>
          <h3 style="color:#1B5E20;margin-bottom:10px;">Real-time Tracking</h3>
          <p style="color:#666;line-height:1.6;">Track your driver live on the map from pickup to destination. Stay informed every step.</p>
        </div>
        <div style="background:#fff;padding:35px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.08);flex:1;min-width:280px;text-align:center;border-top:3px solid #D4AF37;">
          <div style="font-size:40px;margin-bottom:15px;">&#128274;</div>
          <h3 style="color:#1B5E20;margin-bottom:10px;">Safe &amp; Secure</h3>
          <p style="color:#666;line-height:1.6;">Guardian AI safety, panic button, trip monitoring, and vehicle kill switch for maximum security.</p>
        </div>
      </div>
    </section>

    <section style="padding:60px 30px;background:#1B5E20;text-align:center;">
      <h2 style="color:#D4AF37;margin-bottom:40px;font-size:32px;">How It Works</h2>
      <div style="display:flex;justify-content:center;gap:50px;flex-wrap:wrap;max-width:900px;margin:0 auto;">
        <div style="flex:1;min-width:200px;">
          <div style="background:#D4AF37;color:#fff;width:60px;height:60px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin-bottom:15px;">1</div>
          <h4 style="color:#fff;margin-bottom:8px;">Request a Ride</h4>
          <p style="color:rgba(255,255,255,0.7);font-size:14px;">Enter your pickup and destination</p>
        </div>
        <div style="flex:1;min-width:200px;">
          <div style="background:#D4AF37;color:#fff;width:60px;height:60px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin-bottom:15px;">2</div>
          <h4 style="color:#fff;margin-bottom:8px;">Receive Bids</h4>
          <p style="color:rgba(255,255,255,0.7);font-size:14px;">Nearby drivers compete for your ride</p>
        </div>
        <div style="flex:1;min-width:200px;">
          <div style="background:#D4AF37;color:#fff;width:60px;height:60px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin-bottom:15px;">3</div>
          <h4 style="color:#fff;margin-bottom:8px;">Choose &amp; Ride</h4>
          <p style="color:rgba(255,255,255,0.7);font-size:14px;">Pick your preferred driver and enjoy</p>
        </div>
      </div>
    </section>

    <section id="about" style="padding:60px 30px;background:#fff;">
      <div style="display:flex;gap:40px;flex-wrap:wrap;max-width:1100px;margin:0 auto;align-items:center;">
        <div style="flex:1;min-width:300px;">
          <img src="${BASE}/img3.jpeg" style="width:100%;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.1);" alt="Accreditation Point">
        </div>
        <div style="flex:1;min-width:300px;">
          <h2 style="color:#1B5E20;margin-bottom:15px;font-size:28px;">About 9jaRide Pro</h2>
          <p style="color:#666;line-height:1.8;font-size:15px;margin-bottom:15px;">9jaRide Pro Limited is a registered liability company in Nigeria, trademarked under 9jaRide Pro Limited. We are a technology-driven transportation solutions provider, built to serve you.</p>
          <p style="color:#666;line-height:1.8;font-size:15px;margin-bottom:15px;">In partnership with OFMECH Mobility Solutions, we are at the forefront of Africa's Electric Vehicle Revolution - integrating electric vehicles, renewable energy, and smart infrastructure.</p>
          <p style="color:#1B5E20;font-weight:600;font-size:16px;font-style:italic;">"Move Smart. Ride Easy."</p>
        </div>
      </div>
    </section>

    <section style="padding:0;background:#f5f7f5;">
      <div style="display:flex;gap:0;flex-wrap:wrap;max-width:1200px;margin:0 auto;">
        <div style="flex:1;min-width:300px;">
          <img src="${BASE}/img4.jpeg" style="width:100%;height:100%;object-fit:cover;" alt="OFMECH and 9jaRide Pro">
        </div>
        <div style="flex:2;min-width:350px;padding:60px 40px;display:flex;flex-direction:column;justify-content:center;">
          <h2 style="color:#1B5E20;margin-bottom:20px;font-size:28px;">Our Vision</h2>
          <p style="color:#666;line-height:1.8;font-size:15px;margin-bottom:20px;">When we come together we unlock endless possibilities and create solutions that drive us all forward.</p>
          <div style="display:flex;gap:20px;flex-wrap:wrap;">
            <div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;border-left:3px solid #D4AF37;">
              <h4 style="color:#1B5E20;margin-bottom:5px;">Hire Purchase</h4>
              <p style="color:#666;font-size:13px;">Own your vehicle through our flexible HP program</p>
            </div>
            <div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;border-left:3px solid #D4AF37;">
              <h4 style="color:#1B5E20;margin-bottom:5px;">Kill Switch</h4>
              <p style="color:#666;font-size:13px;">Advanced vehicle security and fleet management</p>
            </div>
            <div style="flex:1;min-width:180px;background:#fff;padding:20px;border-radius:10px;border-left:3px solid #D4AF37;">
              <h4 style="color:#1B5E20;margin-bottom:5px;">2.5% Royalty</h4>
              <p style="color:#666;font-size:13px;">Transparent commission on every ride</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="event" style="padding:60px 30px;background:#0F2B15;text-align:center;">
      <h2 style="color:#D4AF37;margin-bottom:30px;font-size:28px;">Upcoming Event</h2>
      <div style="max-width:600px;margin:0 auto;">
        <img src="${BASE}/img5.jpeg" style="width:100%;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.3);" alt="Demo Presentation">
        <p style="color:rgba(255,255,255,0.7);margin-top:20px;font-size:15px;">Join us for the official demo presentation of 9jaRide Pro</p>
        <p style="color:#D4AF37;font-weight:600;font-size:18px;margin-top:10px;">Saturday, 11th April 2026 | 9:00 AM</p>
        <p style="color:rgba(255,255,255,0.7);font-size:14px;">NUT Hall, Agidingbi Primary School, 51 Lateef Jakande Road, Ikeja, Lagos State</p>
      </div>
    </section>

    <section id="contact" style="padding:60px 30px;background:#fff;text-align:center;">
      <h2 style="color:#1B5E20;margin-bottom:20px;font-size:28px;">Get In Touch</h2>
      <p style="color:#666;font-size:16px;margin-bottom:10px;">Ready to ride with us?</p>
      <p style="color:#666;font-size:15px;margin-bottom:5px;">www.9jaridepro.com</p>
      <p style="color:#666;font-size:15px;margin-bottom:20px;">@9jarideproofficial</p>
      <a href="#" style="background:#D4AF37;color:#fff;padding:15px 40px;border-radius:30px;text-decoration:none;font-size:16px;font-weight:600;display:inline-block;">Download the App</a>
    </section>
  </div>`;

  await db.collection("settings").doc("landingPageTemplate").set({
    landingPageTemplate: landingHTML
  });

  await db.collection("settings").doc("footerTemplate").set({
    footerTemplate: '<footer style="background:#0F2B15;padding:40px 30px;color:#fff;"><div style="max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;flex-wrap:wrap;gap:30px;"><div style="flex:1;min-width:250px;"><img src="' + BASE + '/9jaride-pro-logo.svg" style="height:50px;margin-bottom:15px;" alt="9jaRide Pro"><p style="color:rgba(255,255,255,0.6);font-size:14px;line-height:1.6;">Your Trusted Journey Partner.<br>Move Smart. Ride Easy.</p></div><div style="flex:1;min-width:200px;"><h4 style="color:#D4AF37;margin-bottom:15px;">Quick Links</h4><a href="#features" style="color:rgba(255,255,255,0.7);text-decoration:none;display:block;margin-bottom:8px;font-size:14px;">Features</a><a href="#about" style="color:rgba(255,255,255,0.7);text-decoration:none;display:block;margin-bottom:8px;font-size:14px;">About Us</a><a href="#event" style="color:rgba(255,255,255,0.7);text-decoration:none;display:block;margin-bottom:8px;font-size:14px;">Events</a><a href="#contact" style="color:rgba(255,255,255,0.7);text-decoration:none;display:block;font-size:14px;">Contact</a></div><div style="flex:1;min-width:200px;"><h4 style="color:#D4AF37;margin-bottom:15px;">Contact</h4><p style="color:rgba(255,255,255,0.7);font-size:14px;margin-bottom:8px;">www.9jaridepro.com</p><p style="color:rgba(255,255,255,0.7);font-size:14px;margin-bottom:8px;">@9jarideproofficial</p><p style="color:rgba(255,255,255,0.7);font-size:14px;">Lagos, Nigeria</p></div></div><div style="border-top:1px solid rgba(255,255,255,0.1);margin-top:30px;padding-top:20px;text-align:center;"><p style="color:rgba(255,255,255,0.4);font-size:13px;">© 2026 9jaRide Pro Limited. All rights reserved. | Powered by Eagles Partners</p></div></footer>'
  });

  console.log("Landing page updated with all images!");
  process.exit(0);
}
update().catch(e => { console.error(e); process.exit(1); });

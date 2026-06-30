<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bayani Chronicles — Terms & Conditions</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f7fafc;
      --card:#ffffff;
      --accent:#0f172a;
      --muted:#6b7280;
      --primary:#0ea5a4;
      --max-width:1100px;
      font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, 'Helvetica Neue', Arial;
    }

    *{box-sizing:border-box}
    html,body{height:100%;margin:0;background:var(--bg);color:var(--accent);}

    .page{max-width:var(--max-width);margin:32px auto;padding:20px}
    header{display:flex;gap:16px;align-items:center;justify-content:space-between}
    .brand{display:flex;gap:12px;align-items:center}
    .logo{width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,var(--primary),#22c1c3);display:flex;align-items:center;justify-content:center;color:white;font-weight:700}
    h1{font-size:1.375rem;margin:0}
    p.lead{margin:6px 0 0;color:var(--muted)}

    .card{background:var(--card);border-radius:12px;padding:20px;margin-top:20px;box-shadow:0 6px 18px rgba(12,15,20,0.06)}

    nav.toc{display:flex;gap:10px;flex-wrap:wrap}
    nav.toc a{background:#eef2f7;padding:8px 12px;border-radius:8px;text-decoration:none;color:var(--accent);font-weight:600;font-size:0.9rem}

    section{margin-top:18px}
    section h2{margin:0 0 8px;font-size:1.05rem}
    section p, section li{color:var(--muted);line-height:1.5}
    ul{padding-left:18px}

    .meta{display:flex;gap:12px;flex-wrap:wrap;margin-top:10px}
    .meta div{background:#f1f5f9;padding:8px 12px;border-radius:8px}

    /* responsive columns for wider screens */
    .grid{display:grid;grid-template-columns:1fr;gap:18px}
    @media(min-width:900px){
      .grid{grid-template-columns:1fr 320px}
    }

    /* Accordion accessible */
    .accordion{border-radius:10px;overflow:hidden}
    .accordion button{width:100%;text-align:left;padding:12px 16px;border:0;background:#f8fafc;font-weight:600;cursor:pointer}
    .accordion [data-panel]{padding:0 16px 16px;background:white;border-top:1px solid #eef2f7}

    footer{margin-top:28px;text-align:center;color:var(--muted);font-size:0.9rem}

    .actions{display:flex;gap:10px;flex-wrap:wrap}
    .btn{display:inline-block;padding:10px 14px;border-radius:10px;text-decoration:none;font-weight:600}
    .btn.primary{background:var(--primary);color:white}
    .btn.ghost{background:transparent;border:1px solid #e6eef0;color:var(--accent)}

    /* print-friendly */
    @media print{
      body{background:white}
      .page{box-shadow:none}
      nav.toc{display:none}
    }
  </style>
</head>
<body>
  <div class="page">
    <header>
      <div class="brand">
        <div class="logo">BC</div>
        <div>
          <h1>Bayani Chronicles — Terms & Conditions</h1>
          <p class="lead">A student-focused, thesis-built educational Android game about Rizal’s novels.</p>
        </div>
      </div>
      <div class="actions">
        <a class="btn primary" href="#terms">View Terms</a>
        <a class="btn ghost" href="#contact">Contact</a>
      </div>
    </header>

    <div class="grid">
      <main class="card" id="terms">
        <!-- Introduction -->
        <section aria-labelledby="intro">
          <h2 id="intro">1. Introduction</h2>
          <p>Welcome to <strong>Bayani Chronicles</strong>, a 2D Android game application designed to help Filipino students learn about José Rizal’s novels <em>Noli Me Tangere</em> and <em>El Filibusterismo</em> through interactive gameplay. Teachers and administrators may use the admin interface to monitor student progress and scores. This application is a capstone (thesis) project.</p>

          <p>To maintain fairness and security, users must:</p>
          <ul>
            <li>Use only their own account (no multiple accounts per person).</li>
            <li>Avoid playing on another user’s account.</li>
            <li>Administrators must supply a valid email for each user because login requires a One-Time Password (OTP).</li>
            <li>There is currently no logout mechanism to discourage account switching.</li>
          </ul>
        </section>

        <!-- Acceptance -->
        <section aria-labelledby="acceptance">
          <h2 id="acceptance">2. Acceptance of Terms</h2>
          <p>By using Bayani Chronicles, you agree to provide accurate information when registering. You acknowledge that while we secure application data to the best of our ability, we are not responsible for damages caused by downloading the app from unofficial or third-party sources.</p>
          <p>Report bugs, errors, or suspicious activities to:</p>
          <ul>
            <li><strong>chroniclesbayani@gmail.com</strong></li>
            <li><strong>jerichoumayam473@gmail.com</strong> (Lead Developer)</li>
          </ul>
        </section>

        <!-- Eligibility -->
        <section aria-labelledby="eligibility">
          <h2 id="eligibility">3. Eligibility</h2>
          <p>The application is intended for junior high school students and registered administrators (teachers). Players can only play if registered by their teachers. Administrators may be restricted by a Super Admin (e.g., proposed: Sison Central School).</p>
        </section>

        <!-- Intellectual Property -->
        <section aria-labelledby="ip">
          <h2 id="ip">4. Intellectual Property</h2>
          <p>Bayani Chronicles is a thesis project developed at the <strong>University of Eastern Pangasinan (UEP)</strong>. Intellectual property rights belong to the project team and the university. Replication, modification, or resale is prohibited unless expressly permitted by the school.</p>
        </section>

        <!-- User Data & Privacy -->
        <section aria-labelledby="data">
          <h2 id="data">5. User Data & Privacy</h2>
          <p>Administrators must provide name, email, and contact details for account management. Players sign in with a username/email and password. Gameplay data is used solely to tally scores and track progress and does not include sensitive personal information.</p>
          <p>Passwords are encrypted in the database; developers do not have access to plaintext passwords.</p>
        </section>

        <!-- Payments -->
        <section aria-labelledby="payments">
          <h2 id="payments">6. Payments</h2>
          <p>Bayani Chronicles is currently free to use. If any paid features are introduced in the future (such as domain hosting), we will announce them clearly.</p>
        </section>

        <!-- Disclaimer -->
        <section aria-labelledby="disclaimer">
          <h2 id="disclaimer">7. Disclaimer</h2>
          <p>We make reasonable efforts to ensure the application functions correctly but do not guarantee it will be free from bugs or errors. If the game crashes or exhibits anomalies, please report them for inspection. Developers are not responsible for data leaks resulting from factors outside the app’s management.</p>
        </section>

        <!-- Account Suspension -->
        <section aria-labelledby="bans">
          <h2 id="bans">8. Account Suspension & Bans</h2>
          <p>Teachers (administrators) may suspend or ban players under their supervision. Administrators are managed by a Super Admin. Developers are not responsible for actions taken by administrators.</p>
        </section>

        <!-- Governing Law -->
        <section aria-labelledby="law">
          <h2 id="law">9. Governing Law</h2>
          <p>These terms are drafted in the context of an academic thesis project at UEP and are intended primarily for educational use. Any disputes relating to this project should be handled in accordance with applicable university policies and Philippine law.</p>
        </section>

        <!-- Changes -->
        <section aria-labelledby="changes">
          <h2 id="changes">10. Changes to Terms</h2>
          <p>We may update these Terms and Conditions from time to time. Registered users and administrators will be notified by email about any material changes.</p>
        </section>

        <section aria-labelledby="contact" style="margin-top:16px">
          <h2 id="contact">Contact</h2>
          <p>Questions or reports? Email us at <strong>chroniclesbayani@gmail.com</strong> or <strong>jerichoumayam473@gmail.com</strong> (Lead Developer).</p>
        </section>

      </main>

      <aside class="card">
        <nav class="toc" aria-label="Table of contents">
          <a href="#intro">Introduction</a>
          <a href="#acceptance">Acceptance</a>
          <a href="#eligibility">Eligibility</a>
          <a href="#ip">Intellectual Property</a>
          <a href="#data">User Data</a>
          <a href="#payments">Payments</a>
          <a href="#disclaimer">Disclaimer</a>
          <a href="#bans">Bans</a>
          <a href="#law">Governing Law</a>
          <a href="#changes">Changes</a>
          <a href="#contact">Contact</a>
        </nav>

        <div style="margin-top:16px">
          <div class="meta">
            <div><strong>Project</strong><div style="color:var(--muted);font-size:0.9rem">UEP Capstone</div></div>
            <div><strong>Status</strong><div style="color:var(--muted);font-size:0.9rem">Draft</div></div>
            <div><strong>Audience</strong><div style="color:var(--muted);font-size:0.9rem">Students & Teachers</div></div>
          </div>

          <div style="margin-top:14px">
            <details class="accordion" open>
              <summary style="padding:12px 16px;font-weight:700">Quick actions</summary>
              <div data-panel>
                <p style="margin:0 0 8px;color:var(--muted)">You can print this page, copy the HTML, or export it into your project. For academic use only.</p>
                <p style="margin:0;font-size:0.9rem;color:var(--muted)"><strong>Note:</strong> Remove or anonymize any data fields before public release if required by your school.</p>
              </div>
            </details>
          </div>
        </div>
      </aside>
    </div>

    <footer>
      <div style="max-width:720px;margin:0 auto">© University of Eastern Pangasinan — Bayani Chronicles (Thesis Project). This T&C is a draft and may be adapted to university requirements.</div>
    </footer>
  </div>
</body>
</html>

<?php
ini_set('session.cookie_domain', '.despachomx.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();

/* 🔒 BLOQUEO TOTAL */
if (
    !isset($_SESSION['califadns_auth']) ||
    $_SESSION['califadns_auth'] !== true ||
    empty($_SESSION['califadns_user'])
) {
    header("Location: logout.php");
    exit;
}
?>






<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>CalifaDNS MDM Dashboard</title>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>CalifaDNS MDM Dashboard</title>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<header style="position:relative;">
  <h1>CalifaDNS MDM</h1>
  <p>Panel de control remoto de dispositivos</p>

  <div style="position:absolute; top:15px; right:20px;">
    <a href="https://mdm.despachomx.com/index.php"
       style="margin-right:10px; color:white; text-decoration:none; background:#4CAF50; padding:8px 12px; border-radius:5px;">
       Ir a Blog
    </a>

    <a href="auth/logout.php"
       style="color:white; text-decoration:none; background:#f44336; padding:8px 12px; border-radius:5px;">
       Cerrar sesión
    </a>
  </div>
</header>


<section id="devices" class="grid"></section>

<script>
async function loadDevices() {
  const res = await fetch("api.php");
  const data = await res.json();

  const box = document.getElementById("devices");
  box.innerHTML = "";

  for (const id in data) {
    const d = data[id];
    const online = (Date.now()/1000 - d.last_seen) < 20;

    box.innerHTML += `
    <div class="card ${online ? "on" : "off"}">
      <h3>${d.model}</h3>
      <p><b>ID:</b> ${id}</p>
      <p><b>Android:</b> ${d.android}</p>
      <p class="status">${online ? "ONLINE" : "OFFLINE"}</p>
      <p><b>Plan:</b> ${d.plan}</p>
      <p><b>Expira:</b> ${new Date(d.expires_at*1000).toLocaleDateString()}</p>
      <div class="actions">
             
             
              <h4>DESARROLLADOR</h4>
        <button onclick="cmd('${id}','DEV_TEMP_ON')">DEV ON</button>
        <button onclick="cmd('${id}','DEV_TEMP_OFF')">DEV OFF</button> 
       
        <h4>BLOQUEO MDM</h4>
        <button onclick="cmd('${id}','ENTER_KIOSK')" class="lock">BLOQUEAR</button>
        <button onclick="cmd('${id}','EXIT_KIOSK')" class="unlock">DESBLOQUEAR</button>

        <h4>WIFI</h4>
        <button onclick="cmd('${id}','WIFI_LOCK')">LOCK</button>
        <button onclick="cmd('${id}','WIFI_UNLOCK')">UNLOCK</button>

        <h4>DNS</h4>
        <button onclick="cmd('${id}','DNS_LOCK')">LOCK</button>
        <button onclick="cmd('${id}','DNS_UNLOCK')">UNLOCK</button>
        <button onclick="dnsSet('${id}')">SET</button>
        <button onclick="cmd('${id}','DNS_ENABLE')">ENABLE</button>

        <h4>FACTORY RESET</h4>
        <button onclick="cmd('${id}','FR_LOCK')">BLOCK</button>
        <button onclick="cmd('${id}','FR_UNLOCK')">ALLOW</button>
        <button onclick="cmd('${id}','FACTORY_RESET')" class="danger">RESET</button>

        <h4>SYSTEM</h4>
        <button onclick="cmd('${id}','STATUSBAR_LOCK')">HIDE STATUSBAR</button>
        <button onclick="cmd('${id}','STATUSBAR_UNLOCK')">SHOW STATUSBAR</button>
        <button onclick="cmd('${id}','UPDATES_OFF')">UPDATES OFF</button>
        <button onclick="cmd('${id}','UPDATES_ON')">UPDATES ON</button>
        <button onclick="cmd('${id}','REBOOT')" class="danger">REBOOT</button>
        
        <h4>SUBSCRIPCIÓN</h4>
        <input type="date" 
        onchange="setExpire('${id}', this.value)" />

<button onclick="setStatus('${id}','ACTIVE')">ACTIVAR</button>
<button onclick="setStatus('${id}','BLOCKED')" class="danger">BLOQUEAR</button>

      </div>
    </div>`;
  }
}

async function cmd(device, command) {
  await fetch("server/set_command.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `token=7429&device=${device}&cmd=${command}`
  });
}

/* DNS MANUAL REAL */
function dnsSet(device){
  const dns = prompt("Introduce el DNS hostname:");
  if(!dns) return;

  fetch("server/set_command.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `token=7429&device=${device}&cmd=DNS_SET&value=${encodeURIComponent(dns)}`
  });
}

setInterval(loadDevices, 3000);
loadDevices();
</script>

</body>
</html>

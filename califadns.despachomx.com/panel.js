const TOKEN = "7429";
const devicesBox = document.getElementById("devices");
const logBox = document.getElementById("logbox");

const cards = {}; // cache DOM por dispositivo

// ================================
// 🔄 CARGA DE DISPOSITIVOS
// ================================
async function loadDevices(){
  const res = await fetch("api.php");
  const data = await res.json();

  for(const id in data){
    const d = data[id];
    const online = (Date.now()/1000 - d.last_seen) < 20;

    // ───────── CREAR CARD SOLO UNA VEZ ─────────
    if(!cards[id]){
      const card = document.createElement("div");
      card.className = "card";

      card.innerHTML = `
        <h3 class="model"></h3>
        <small class="info"></small>

        <div class="section">
          <h4>KIOSK</h4>
          <button data-cmd="ENTER_KIOSK">ENTER</button>
          <button data-cmd="EXIT_KIOSK">EXIT</button>
        </div>

        <div class="section">
          <h4>MANTENIMIENTO</h4>
          <button data-cmd="DEV_TEMP_ON">DEV ON</button>
          <button data-cmd="DEV_TEMP_OFF">DEV OFF</button>
        </div>

        <div class="section">
          <h4>WIFI</h4>
          <button data-cmd="WIFI_LOCK">LOCK</button>
          <button data-cmd="WIFI_UNLOCK">UNLOCK</button>
        </div>

        <div class="section">
          <h4>DNS</h4>
          <button data-cmd="DNS_LOCK">LOCK</button>
          <button data-cmd="DNS_UNLOCK">UNLOCK</button>
          <button class="dns-set">SET</button>
        </div>

        <div class="section">
          <h4>SUSCRIPCIÓN</h4>
          <input type="date" class="expire">
          <button class="activate">ACTIVAR</button>
          <button class="block">BLOQUEAR</button>
        </div>

        <div class="section">
          <h4>SISTEMA</h4>
          <button data-cmd="REBOOT">REBOOT</button>
        </div>
      `;

      // botones por comando
      card.querySelectorAll("[data-cmd]").forEach(btn=>{
        btn.onclick = ()=> send(id, btn.dataset.cmd);
      });

      // DNS manual
      card.querySelector(".dns-set").onclick = ()=>{
        const dns = prompt("DNS hostname:");
        if(dns) send(id, "DNS_SET", dns);
      };

      // fecha expiración
      card.querySelector(".expire").onchange = e=>{
        setExpire(id, e.target.value);
      };

      // estado
      card.querySelector(".activate").onclick = ()=> setStatus(id,"ACTIVE");
      card.querySelector(".block").onclick    = ()=> setStatus(id,"BLOCKED");

      devicesBox.appendChild(card);
      cards[id] = card;
    }

    // ───────── ACTUALIZAR DATOS ─────────
    const card = cards[id];
    card.className = "card " + (online ? "online":"offline");

    card.querySelector(".model").textContent = d.model;
    card.querySelector(".info").innerHTML = `
      ID: ${id}<br>
      Android ${d.android}<br>
      Estado: <b>${d.status ?? "ACTIVE"}</b>
    `;

    if(d.expires_at){
      card.querySelector(".expire").value =
        new Date(d.expires_at*1000).toISOString().split("T")[0];
    }
  }
}

// ================================
// 📤 COMANDOS
// ================================
async function send(id, cmd, value=""){
  let body = `token=${TOKEN}&device=${id}&cmd=${cmd}`;
  if(value) body += `&value=${encodeURIComponent(value)}`;

  await fetch("server/set_command.php",{
    method:"POST",
    headers:{ "Content-Type":"application/x-www-form-urlencoded" },
    body
  });
}

async function setExpire(id, date){
  if(!date) return;
  const ts = Math.floor(new Date(date).getTime()/1000);

  await fetch("server/update_device.php",{
    method:"POST",
    headers:{ "Content-Type":"application/x-www-form-urlencoded" },
    body:`token=${TOKEN}&device=${id}&expires=${ts}`
  });
}

async function setStatus(id, status){
  await fetch("server/update_device.php",{
    method:"POST",
    headers:{ "Content-Type":"application/x-www-form-urlencoded" },
    body:`token=${TOKEN}&device=${id}&status=${status}`
  });
}

// ================================
// 📜 LOGS
// ================================
async function loadLogs(){
  const r = await fetch("server/logs/latest.log?"+Date.now());
  if(r.ok) logBox.textContent = await r.text();
}

// ================================
// ⏱️ LOOP
// ================================
setInterval(loadDevices, 3000);
setInterval(loadLogs, 2000);
loadDevices();

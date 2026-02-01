async function loadDevices() {
  const res = await fetch("api.php");
  const data = await res.json();

  const box = document.getElementById("devices");
  box.innerHTML = "";

  for (const id in data) {
    const d = data[id];
    const online = (Date.now()/1000 - d.last_seen) < 15;

    box.innerHTML += `
      <div class="card ${online?"on":"off"}">
        <h3>${d.model}</h3>
        <p>ID: ${id}</p>
        <p>Android: ${d.android}</p>
        <p>${online?"🟢 ONLINE":"🔴 OFFLINE"}</p>
        <button onclick="cmd('${id}','ENTER_KIOSK')">LOCK</button>
        <button onclick="cmd('${id}','EXIT_KIOSK')">UNLOCK</button>

      </div>
    `;
  }
}

async function cmd(id, c) {
  await fetch("server/set_command.php", {
    method:"POST",
    headers:{ "Content-Type":"application/x-www-form-urlencoded" },
    body:`token=7429&cmd=${c}`
  });
}

setInterval(loadDevices, 3000);
loadDevices();

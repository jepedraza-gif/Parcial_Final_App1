const token = sessionStorage.getItem('token');
const mesasTbody = document.querySelector("#mesasTabla tbody");
const crearMesaForm = document.forms["crearMesaForm"];
const mensajeCrear = document.getElementById("mensajeCrear");
const editarMesaForm = document.forms["editarMesaForm"];
const mensajeEditar = document.getElementById("mensajeEditar");
const cambiarEstadoForm = document.forms["cambiarEstadoForm"];
const mensajeEstado = document.getElementById("mensajeEstado");

if (!token) {
  window.location.href = "index.html";
}

// Listar mesas
const listarMesas = async () => {
  try {
    const response = await fetch("http://127.0.0.1:8002/mesas", {
      method: "get",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const mesas = await response.json();
      mostrarMesas(mesas);
    } else {
      mesasTbody.innerHTML = "<tr><td colspan='5'>Error al cargar mesas</td></tr>";
    }
  } catch (error) {
    mesasTbody.innerHTML = "<tr><td colspan='5'>Error de conexión</td></tr>";
  }
};

const mostrarMesas = (mesas) => {
  mesasTbody.innerHTML = "";
  mesas.forEach((mesa) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = mesa.id;
    tr.appendChild(tdId);

    const tdNumero = document.createElement("td");
    tdNumero.textContent = mesa.numero;
    tr.appendChild(tdNumero);

    const tdCapacidad = document.createElement("td");
    tdCapacidad.textContent = mesa.capacidad;
    tr.appendChild(tdCapacidad);

    const tdEstado = document.createElement("td");
    tdEstado.textContent = mesa.estado;
    tr.appendChild(tdEstado);

    const tdAcciones = document.createElement("td");

    // Botón seleccionar para editar
    const btnEditar = document.createElement("a");
    btnEditar.textContent = "Editar";
    btnEditar.href = "#editarMesaCard";
    btnEditar.style.display = "inline-block";
    btnEditar.style.marginRight = "5px";
    btnEditar.addEventListener("click", () => {
      document.getElementById("mesaIdEditar").value = mesa.id;
      document.getElementById("nuevaCapacidad").value = mesa.capacidad;
      document.getElementById("nuevoEstadoEditar").value = mesa.estado;
    });
    tdAcciones.appendChild(btnEditar);

    // Botón seleccionar para cambiar estado
    const btnEstado = document.createElement("a");
    btnEstado.textContent = "Cambiar estado";
    btnEstado.href = "#cambiarEstadoCard";
    btnEstado.style.display = "inline-block";
    btnEstado.addEventListener("click", () => {
      document.getElementById("mesaIdEstado").value = mesa.id;
      document.getElementById("nuevoEstado").value = mesa.estado;
    });
    tdAcciones.appendChild(btnEstado);

    tr.appendChild(tdAcciones);
    mesasTbody.appendChild(tr);
  });
};

// Crear mesa
crearMesaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const data = {
    numero: crearMesaForm["numero"].value,
    capacidad: crearMesaForm["capacidad"].value,
    estado: crearMesaForm["estado"].value,
  };

  try {
    const response = await fetch("http://127.0.0.1:8002/mesas", {
      method: "post",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 201) {
      mensajeCrear.textContent = "Mesa creada correctamente";
      mensajeCrear.className = "success";
      crearMesaForm.reset();
      listarMesas();
    } else {
      const error = await response.json();
      mensajeCrear.textContent = error.error || "Error al crear mesa";
      mensajeCrear.className = "error";
    }
  } catch (error) {
    mensajeCrear.textContent = "Error de conexión";
    mensajeCrear.className = "error";
  }
});

// Editar mesa (solo capacidad)
editarMesaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = editarMesaForm["id"].value;
  const data = {};
  if (editarMesaForm["capacidad"].value) data.capacidad = editarMesaForm["capacidad"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8002/mesas/${id}`, {
      method: "put",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 200) {
      mensajeEditar.textContent = "Mesa actualizada correctamente";
      mensajeEditar.className = "success";
      editarMesaForm.reset();
      listarMesas();
    } else {
      const error = await response.json();
      mensajeEditar.textContent = error.error || "Error al actualizar mesa";
      mensajeEditar.className = "error";
    }
  } catch (error) {
    mensajeEditar.textContent = "Error de conexión";
    mensajeEditar.className = "error";
  }
});

// Cambiar estado de mesa (usa PUT como Editar Mesa)
cambiarEstadoForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = cambiarEstadoForm["id"].value;
  const estado = cambiarEstadoForm["estado"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8002/mesas/${id}`, {
      method: "put",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ estado: estado }),
    });

    if (response.status === 200) {
      mensajeEstado.textContent = "Estado actualizado correctamente";
      mensajeEstado.className = "success";
      cambiarEstadoForm.reset();
      listarMesas();
    } else {
      const error = await response.json();
      mensajeEstado.textContent = error.error || "Error al cambiar estado";
      mensajeEstado.className = "error";
    }
  } catch (error) {
    mensajeEstado.textContent = "Error de conexión";
    mensajeEstado.className = "error";
  }
});

listarMesas();

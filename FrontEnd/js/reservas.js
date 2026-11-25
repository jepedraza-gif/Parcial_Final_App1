const token = sessionStorage.getItem('token');
const reservasTbody = document.querySelector("#reservasTabla tbody");
const reservaForm = document.forms["reservaForm"];
const mensajeCrear = document.getElementById("mensajeCrear");
const editarReservaForm = document.forms["editarReservaForm"];
const mensajeEditar = document.getElementById("mensajeEditar");
const cancelarReservaForm = document.forms["cancelarReservaForm"];
const mensajeCancelar = document.getElementById("mensajeCancelar");
const buscarReservaForm = document.forms["buscarReservaForm"];
const btnLimpiarBusqueda = document.getElementById("btnLimpiarBusqueda");
const mensajeBuscar = document.getElementById("mensajeBuscar");

if (!token) {
  window.location.href = "index.html";
}

// Listar reservas
const listarReservas = async () => {
  try {
    const response = await fetch("http://127.0.0.1:8002/reservas", {
      method: "get",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const reservas = await response.json();
      mostrarReservas(reservas);
    } else {
      reservasTbody.innerHTML = "<tr><td colspan='9'>Error al cargar reservas</td></tr>";
    }
  } catch (error) {
    reservasTbody.innerHTML = "<tr><td colspan='9'>Error de conexión</td></tr>";
  }
};

const mostrarReservas = (reservas) => {
  reservasTbody.innerHTML = "";
  reservas.forEach((reserva) => {
    const tr = document.createElement("tr");

    const celdas = [
      reserva.id,
      reserva.nombre_cliente,
      reserva.telefono_cliente,
      reserva.cantidad_personas,
      reserva.fecha,
      reserva.hora,
      reserva.mesa_id,
      reserva.estado,
    ];

    celdas.forEach((valor) => {
      const td = document.createElement("td");
      td.textContent = valor;
      tr.appendChild(td);
    });

    const tdAcciones = document.createElement("td");
    const btnSeleccionar = document.createElement("a");
    btnSeleccionar.textContent = "Editar";
    btnSeleccionar.href = "#editarReservaCard";
    btnSeleccionar.style.display = "inline-block";
    btnSeleccionar.addEventListener("click", () => {
      document.getElementById("reservaId").value = reserva.id;
      document.getElementById("nuevaFecha").value = reserva.fecha;
      document.getElementById("nuevaHora").value = reserva.hora;
      document.getElementById("nuevaMesa").value = reserva.mesa_id;
      document.getElementById("nuevaCantidad").value = reserva.cantidad_personas;
      document.getElementById("nuevoEstado").value = reserva.estado;
      document.getElementById("nuevasObservaciones").value = reserva.observaciones || "";
    });
    tdAcciones.appendChild(btnSeleccionar);
    tr.appendChild(tdAcciones);

    reservasTbody.appendChild(tr);
  });
};

// Crear reserva
reservaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const data = {
    nombre_cliente: reservaForm["nombre_cliente"].value,
    telefono_cliente: reservaForm["telefono_cliente"].value,
    cantidad_personas: reservaForm["cantidad_personas"].value,
    fecha: reservaForm["fecha"].value,
    hora: reservaForm["hora"].value,
    mesa_id: reservaForm["mesa_id"].value,
    observaciones: reservaForm["observaciones"].value || null,
  };

  try {
    const response = await fetch("http://127.0.0.1:8002/reservas", {
      method: "post",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 201) {
      mensajeCrear.textContent = "Reserva creada correctamente";
      mensajeCrear.className = "success";
      reservaForm.reset();
      listarReservas();
    } else {
      const error = await response.json();
      mensajeCrear.textContent = error.error || "Error al crear reserva";
      mensajeCrear.className = "error";
    }
  } catch (error) {
    mensajeCrear.textContent = "Error de conexión";
    mensajeCrear.className = "error";
  }
});

// Editar reserva (incluye estado)
editarReservaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = editarReservaForm["id"].value;
  const data = {};
  if (editarReservaForm["fecha"].value) data.fecha = editarReservaForm["fecha"].value;
  if (editarReservaForm["hora"].value) data.hora = editarReservaForm["hora"].value;
  if (editarReservaForm["mesa_id"].value) data.mesa_id = editarReservaForm["mesa_id"].value;
  if (editarReservaForm["cantidad_personas"].value) data.cantidad_personas = editarReservaForm["cantidad_personas"].value;
  if (editarReservaForm["estado"].value) data.estado = editarReservaForm["estado"].value;
  if (editarReservaForm["observaciones"].value) data.observaciones = editarReservaForm["observaciones"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8002/reservas/${id}`, {
      method: "put",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 200) {
      mensajeEditar.textContent = "Reserva actualizada correctamente";
      mensajeEditar.className = "success";
      editarReservaForm.reset();
      listarReservas();
    } else {
      const error = await response.json();
      mensajeEditar.textContent = error.error || "Error al actualizar reserva";
      mensajeEditar.className = "error";
    }
  } catch (error) {
    mensajeEditar.textContent = "Error de conexión";
    mensajeEditar.className = "error";
  }
});

// Cancelar reserva
cancelarReservaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = cancelarReservaForm["id"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8002/reservas/${id}`, {
      method: "delete",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      mensajeCancelar.textContent = "Reserva cancelada correctamente";
      mensajeCancelar.className = "success";
      cancelarReservaForm.reset();
      listarReservas();
    } else {
      const error = await response.json();
      mensajeCancelar.textContent = error.error || "Error al cancelar reserva";
      mensajeCancelar.className = "error";
    }
  } catch (error) {
    mensajeCancelar.textContent = "Error de conexión";
    mensajeCancelar.className = "error";
  }
});

// Buscar reservas por fecha, cliente y estado
buscarReservaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const fecha = buscarReservaForm["fecha"].value;
  const cliente = buscarReservaForm["cliente"].value;
  const estado = buscarReservaForm["estado"].value;

  try {
    let url = "http://127.0.0.1:8002/reservas";
    const params = [];
    if (fecha) params.push("fecha=" + encodeURIComponent(fecha));
    if (cliente) params.push("cliente=" + encodeURIComponent(cliente));
    if (estado) params.push("estado=" + encodeURIComponent(estado));
    if (params.length > 0) url += "?" + params.join("&");

    const response = await fetch(url, {
      method: "get",
      headers: {
        "Authorization": "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const reservas = await response.json();
      mostrarReservas(reservas);
      mensajeBuscar.textContent = "Búsqueda realizada";
      mensajeBuscar.className = "success";
    } else {
      mensajeBuscar.textContent = "Error en la búsqueda";
      mensajeBuscar.className = "error";
    }
  } catch (error) {
    mensajeBuscar.textContent = "Error de conexión";
    mensajeBuscar.className = "error";
  }
});

// Limpiar búsqueda
btnLimpiarBusqueda.addEventListener("click", () => {
  buscarReservaForm.reset();
  mensajeBuscar.textContent = "";
  mensajeBuscar.className = "";
  listarReservas();
});

listarReservas();

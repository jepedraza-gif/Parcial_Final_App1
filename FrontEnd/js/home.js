const token = sessionStorage.getItem("token");
const userName = document.getElementById("user-name");
const userRol = document.getElementById("user-rol");
const cerrarSesionBtn = document.getElementById("cerrarSesionBtn");
const mensaje = document.getElementById("mensaje");

// Redirigir a login si no hay token
if (!token) {
  window.location.href = "index.html";
}

// Validar sesión con ms-auth
const validarSesion = async () => {
  try {
    const response = await fetch("http://127.0.0.1:8001/validar-sesion", {
      method: "get",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const data = await response.json();
      userName.textContent = data.usuario.nombre;
      userRol.textContent = data.usuario.rol;
    } else {
      sessionStorage.clear();
      window.location.href = "index.html";
    }
  } catch (error) {
    sessionStorage.clear();
    window.location.href = "index.html";
  }
};

validarSesion();

// Cerrar sesión
const logout = async () => {
  try {
    await fetch("http://127.0.0.1:8001/logout", {
      method: "post",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });
  } catch (error) {
    console.error("Error al cerrar sesión:", error);
  } finally {
    sessionStorage.clear();
    window.location.href = "index.html";
  }
};

cerrarSesionBtn.addEventListener("click", logout);

// Botones de navegación
document.getElementById("btnMesas").addEventListener("click", () => {
  window.location.href = "mesas.html";
});

document.getElementById("btnReservas").addEventListener("click", () => {
  window.location.href = "reservas.html";
});

document.getElementById("btnProductos").addEventListener("click", () => {
  window.location.href = "productos.html";
});

document.getElementById("btnPedidos").addEventListener("click", () => {
  window.location.href = "pedidos.html";
});

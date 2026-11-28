const loginForm = document.forms["login-form"];
const mensaje = document.getElementById("mensaje");

const login = async () => {
  try {
    const data = {
      usuario: loginForm["usuario"].value,
      contrasena: loginForm["contrasena"].value,
    };

    const response = await fetch("http://127.0.0.1:8001/login", {
      method: "post",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 200) {
      const resultado = await response.json();
      sessionStorage.setItem("token", resultado.token);
      sessionStorage.setItem("user.name", resultado.usuario.nombre);
      sessionStorage.setItem("user.usuario", resultado.usuario.usuario);
      sessionStorage.setItem("user.rol", resultado.usuario.rol);
      sessionStorage.setItem("user.id", resultado.usuario.id);

      mensaje.textContent = "";
      mensaje.className = "";
      window.location.href = "home.html";
    } else {
      const error = await response.json();
      mensaje.textContent = error.error || "Datos incorrectos";
      mensaje.className = "error";
    }
  } catch (error) {
    mensaje.textContent = "Error de conexión con el servidor";
    mensaje.className = "error";
  }
};

loginForm.addEventListener("submit", (evt) => {
  evt.preventDefault();
  login();
});

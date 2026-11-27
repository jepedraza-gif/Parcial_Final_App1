const token = sessionStorage.getItem("token");
const categoriasTbody = document.querySelector("#categoriasTabla tbody");
const productosTbody = document.querySelector("#productosTabla tbody");

const categoriaForm = document.forms["categoriaForm"];
const mensajeCrearCategoria = document.getElementById("mensajeCrearCategoria");
const editarCategoriaForm = document.forms["editarCategoriaForm"];
const mensajeEditarCategoria = document.getElementById(
  "mensajeEditarCategoria",
);
const eliminarCategoriaForm = document.forms["eliminarCategoriaForm"];
const mensajeEliminarCategoria = document.getElementById(
  "mensajeEliminarCategoria",
);

const productoForm = document.forms["productoForm"];
const mensajeCrearProducto = document.getElementById("mensajeCrearProducto");
const editarProductoForm = document.forms["editarProductoForm"];
const mensajeEditarProducto = document.getElementById("mensajeEditarProducto");
const eliminarProductoForm = document.forms["eliminarProductoForm"];
const mensajeEliminarProducto = document.getElementById(
  "mensajeEliminarProducto",
);

const buscarProductoForm = document.forms["buscarProductoForm"];
const btnLimpiarBusquedaProducto = document.getElementById(
  "btnLimpiarBusquedaProducto",
);
const mensajeBuscarProducto = document.getElementById("mensajeBuscarProducto");

if (!token) {
  window.location.href = "index.html";
}

const listarCategorias = async () => {
  try {
    const response = await fetch("http://127.0.0.1:8003/categorias", {
      method: "get",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const categorias = await response.json();
      mostrarCategorias(categorias);
    } else {
      categoriasTbody.innerHTML =
        "<tr><td colspan='4'>Error al cargar categorías</td></tr>";
    }
  } catch (error) {
    categoriasTbody.innerHTML =
      "<tr><td colspan='4'>Error de conexión</td></tr>";
  }
};

const mostrarCategorias = (categorias) => {
  categoriasTbody.innerHTML = "";
  categorias.forEach((categoria) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = categoria.id;
    tr.appendChild(tdId);

    const tdNombre = document.createElement("td");
    tdNombre.textContent = categoria.nombre;
    tr.appendChild(tdNombre);

    const tdDescripcion = document.createElement("td");
    tdDescripcion.textContent = categoria.descripcion || "";
    tr.appendChild(tdDescripcion);

    const tdAcciones = document.createElement("td");

    const btnEditar = document.createElement("a");
    btnEditar.textContent = "Editar";
    btnEditar.href = "#editarCategoriaCard";
    btnEditar.style.display = "inline-block";
    btnEditar.style.marginRight = "5px";
    btnEditar.addEventListener("click", () => {
      document.getElementById("categoriaId").value = categoria.id;
      document.getElementById("nuevoNombreCategoria").value = categoria.nombre;
      document.getElementById("nuevaDescripcionCategoria").value =
        categoria.descripcion || "";
    });
    tdAcciones.appendChild(btnEditar);

    const btnEliminar = document.createElement("a");
    btnEliminar.textContent = "Eliminar";
    btnEliminar.href = "#eliminarCategoriaCard";
    btnEliminar.style.display = "inline-block";
    btnEliminar.addEventListener("click", () => {
      document.getElementById("eliminarCategoriaId").value = categoria.id;
    });
    tdAcciones.appendChild(btnEliminar);

    tr.appendChild(tdAcciones);
    categoriasTbody.appendChild(tr);
  });
};

categoriaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const data = {
    nombre: categoriaForm["nombre"].value,
    descripcion: categoriaForm["descripcion"].value || null,
  };

  try {
    const response = await fetch("http://127.0.0.1:8003/categorias", {
      method: "post",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 201) {
      mensajeCrearCategoria.textContent = "Categoría creada correctamente";
      mensajeCrearCategoria.className = "success";
      categoriaForm.reset();
      listarCategorias();
    } else {
      const error = await response.json();
      mensajeCrearCategoria.textContent =
        error.error || "Error al crear categoría";
      mensajeCrearCategoria.className = "error";
    }
  } catch (error) {
    mensajeCrearCategoria.textContent = "Error de conexión";
    mensajeCrearCategoria.className = "error";
  }
});

editarCategoriaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = editarCategoriaForm["id"].value;
  const data = {};
  if (editarCategoriaForm["nombre"].value)
    data.nombre = editarCategoriaForm["nombre"].value;
  if (editarCategoriaForm["descripcion"].value)
    data.descripcion = editarCategoriaForm["descripcion"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8003/categorias/${id}`, {
      method: "put",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 200) {
      mensajeEditarCategoria.textContent =
        "Categoría actualizada correctamente";
      mensajeEditarCategoria.className = "success";
      editarCategoriaForm.reset();
      listarCategorias();
    } else {
      const error = await response.json();
      mensajeEditarCategoria.textContent =
        error.error || "Error al actualizar categoría";
      mensajeEditarCategoria.className = "error";
    }
  } catch (error) {
    mensajeEditarCategoria.textContent = "Error de conexión";
    mensajeEditarCategoria.className = "error";
  }
});

eliminarCategoriaForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = eliminarCategoriaForm["id"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8003/categorias/${id}`, {
      method: "delete",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      mensajeEliminarCategoria.textContent =
        "Categoría eliminada correctamente";
      mensajeEliminarCategoria.className = "success";
      eliminarCategoriaForm.reset();
      listarCategorias();
    } else {
      const error = await response.json();
      mensajeEliminarCategoria.textContent =
        error.error || "Error al eliminar categoría";
      mensajeEliminarCategoria.className = "error";
    }
  } catch (error) {
    mensajeEliminarCategoria.textContent = "Error de conexión";
    mensajeEliminarCategoria.className = "error";
  }
});

const listarProductos = async () => {
  try {
    const response = await fetch("http://127.0.0.1:8003/productos", {
      method: "get",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const productos = await response.json();
      mostrarProductos(productos);
    } else {
      productosTbody.innerHTML =
        "<tr><td colspan='6'>Error al cargar productos</td></tr>";
    }
  } catch (error) {
    productosTbody.innerHTML =
      "<tr><td colspan='6'>Error de conexión</td></tr>";
  }
};

const mostrarProductos = (productos) => {
  productosTbody.innerHTML = "";
  productos.forEach((producto) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = producto.id;
    tr.appendChild(tdId);

    const tdNombre = document.createElement("td");
    tdNombre.textContent = producto.nombre;
    tr.appendChild(tdNombre);

    const tdCategoria = document.createElement("td");
    tdCategoria.textContent = producto.categoria
      ? producto.categoria.nombre
      : producto.categoria_id;
    tr.appendChild(tdCategoria);

    const tdPrecio = document.createElement("td");
    tdPrecio.textContent = producto.precio;
    tr.appendChild(tdPrecio);

    const tdDisponible = document.createElement("td");
    tdDisponible.textContent = producto.disponible ? "Sí" : "No";
    tr.appendChild(tdDisponible);

    const tdAcciones = document.createElement("td");

    const btnEditar = document.createElement("a");
    btnEditar.textContent = "Editar";
    btnEditar.href = "#editarProductoCard";
    btnEditar.style.display = "inline-block";
    btnEditar.style.marginRight = "5px";
    btnEditar.addEventListener("click", () => {
      document.getElementById("productoId").value = producto.id;
      document.getElementById("nuevoNombreProducto").value = producto.nombre;
      document.getElementById("nuevaDescripcionProducto").value =
        producto.descripcion || "";
      document.getElementById("nuevoPrecioProducto").value = producto.precio;
      document.getElementById("nuevaCategoriaProducto").value =
        producto.categoria_id;
      document.getElementById("nuevoDisponibleProducto").value =
        producto.disponible;
    });
    tdAcciones.appendChild(btnEditar);

    const btnEliminar = document.createElement("a");
    btnEliminar.textContent = "Eliminar";
    btnEliminar.href = "#eliminarProductoCard";
    btnEliminar.style.display = "inline-block";
    btnEliminar.addEventListener("click", () => {
      document.getElementById("eliminarProductoId").value = producto.id;
    });
    tdAcciones.appendChild(btnEliminar);

    tr.appendChild(tdAcciones);
    productosTbody.appendChild(tr);
  });
};

productoForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const data = {
    nombre: productoForm["nombre"].value,
    descripcion: productoForm["descripcion"].value || null,
    precio: productoForm["precio"].value,
    categoria_id: productoForm["categoria_id"].value,
    disponible: productoForm["disponible"].value === "true",
  };

  try {
    const response = await fetch("http://127.0.0.1:8003/productos", {
      method: "post",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 201) {
      mensajeCrearProducto.textContent = "Producto creado correctamente";
      mensajeCrearProducto.className = "success";
      productoForm.reset();
      listarProductos();
    } else {
      const error = await response.json();
      mensajeCrearProducto.textContent =
        error.error || "Error al crear producto";
      mensajeCrearProducto.className = "error";
    }
  } catch (error) {
    mensajeCrearProducto.textContent = "Error de conexión";
    mensajeCrearProducto.className = "error";
  }
});

editarProductoForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = editarProductoForm["id"].value;
  const data = {};
  if (editarProductoForm["nombre"].value)
    data.nombre = editarProductoForm["nombre"].value;
  if (editarProductoForm["descripcion"].value)
    data.descripcion = editarProductoForm["descripcion"].value;
  if (editarProductoForm["precio"].value)
    data.precio = editarProductoForm["precio"].value;
  if (editarProductoForm["categoria_id"].value)
    data.categoria_id = editarProductoForm["categoria_id"].value;
  data.disponible = editarProductoForm["disponible"].value === "true";

  try {
    const response = await fetch(`http://127.0.0.1:8003/productos/${id}`, {
      method: "put",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.status === 200) {
      mensajeEditarProducto.textContent = "Producto actualizado correctamente";
      mensajeEditarProducto.className = "success";
      editarProductoForm.reset();
      listarProductos();
    } else {
      const error = await response.json();
      mensajeEditarProducto.textContent =
        error.error || "Error al actualizar producto";
      mensajeEditarProducto.className = "error";
    }
  } catch (error) {
    mensajeEditarProducto.textContent = "Error de conexión";
    mensajeEditarProducto.className = "error";
  }
});

eliminarProductoForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const id = eliminarProductoForm["id"].value;

  try {
    const response = await fetch(`http://127.0.0.1:8003/productos/${id}`, {
      method: "delete",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      mensajeEliminarProducto.textContent = "Producto eliminado correctamente";
      mensajeEliminarProducto.className = "success";
      eliminarProductoForm.reset();
      listarProductos();
    } else {
      const error = await response.json();
      mensajeEliminarProducto.textContent =
        error.error || "Error al eliminar producto";
      mensajeEliminarProducto.className = "error";
    }
  } catch (error) {
    mensajeEliminarProducto.textContent = "Error de conexión";
    mensajeEliminarProducto.className = "error";
  }
});

buscarProductoForm.addEventListener("submit", async (evt) => {
  evt.preventDefault();
  const categoriaId = buscarProductoForm["categoria_id"].value;
  const disponible = buscarProductoForm["disponible"].value;

  try {
    let url = "http://127.0.0.1:8003/productos";
    const params = [];
    if (categoriaId)
      params.push("categoria_id=" + encodeURIComponent(categoriaId));
    if (disponible) params.push("disponible=" + encodeURIComponent(disponible));
    if (params.length > 0) url += "?" + params.join("&");

    const response = await fetch(url, {
      method: "get",
      headers: {
        Authorization: "Bearer " + token,
        "Content-Type": "application/json",
      },
    });

    if (response.status === 200) {
      const productos = await response.json();
      mostrarProductos(productos);
      mensajeBuscarProducto.textContent = "Búsqueda realizada";
      mensajeBuscarProducto.className = "success";
      location.href = "#listadoProductos";
    } else {
      mensajeBuscarProducto.textContent = "Error en la búsqueda";
      mensajeBuscarProducto.className = "error";
    }
  } catch (error) {
    mensajeBuscarProducto.textContent = "Error de conexión";
    mensajeBuscarProducto.className = "error";
  }
});

btnLimpiarBusquedaProducto.addEventListener("click", () => {
  buscarProductoForm.reset();
  mensajeBuscarProducto.textContent = "";
  mensajeBuscarProducto.className = "";
  listarProductos();
  location.href = "#listadoProductos";
});

listarCategorias();
listarProductos();

const { createApp } = Vue;

createApp({
  data() {
    return {
      servicios: [
        { id: 1, nombre: "Ensayos 2 horas", descripcion: "Ensayo de 2 horas en sala equipada.", precio: "$6,000" },
        { id: 2, nombre: "Ensayos 4 horas", descripcion: "Ensayo de 4 horas con equipos premium.", precio: "$8,000" },
        { id: 3, nombre: "Festivales", descripcion: "Servicio completo para festivales.", precio: "$12,000" },
        { id: 4, nombre: "Peñas y Eventos", descripcion: "Cobertura de audio para eventos.", precio: "$10,000" },
        { id: 5, nombre: "Alquiler de Equipos", descripcion: "Precio a convenir según necesidades.", precio: "Precio a convenir" },
        { id: 6, nombre: "Reparación de Audio", descripcion: "Servicio técnico especializado.", precio: "Precio a convenir" },
      ],
    };
  },
  methods: {
    irServicios() {
      document.body.style.animation = "fadeOut 0.8s ease-in-out forwards";
      setTimeout(() => {
        window.location.href = "servicios.html";
      }, 800);
    },
    reservar(servicio) {
      document.body.style.animation = "fadeOut 0.8s ease-in-out forwards";
      setTimeout(() => {
        if (servicio.nombre === "Reparación de Audio") {
          window.location.href = "reparacion.php";
        } else {
          window.location.href = "reservas.php";
        }
      }, 800);
    },
  },
}).mount("#app");

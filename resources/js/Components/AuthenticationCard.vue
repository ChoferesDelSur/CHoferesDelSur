<template>
    <div class="w-full h-full bg-gray-100 fondo">
        <div class=" min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 filtro">
            <div>
                <slot name="logo" />
            </div>

            <div class="w-full sm:w-1/2 mt-6 px-6 py-4 bg-transparent shadow-md overflow-hidden sm:rounded-lg glass-effect">
                <slot />
            </div>
        </div>
    </div>
</template>
<style>
.fondo {
    /* Ubicación de la imagen */
    background-image: url("https://i.postimg.cc/brJVyT4p/Fondo2.jpg");
    /* Para que la imagen de fondo se adapte al tamaño de la pantalla */
    background-size: cover;    
}

.filtro {
    backdrop-filter: blur(4px);
}


.glass-effect {
    background: rgba(255, 255, 255, 0.3); /* Fondo blanco con transparencia */
    backdrop-filter: blur(10px); /* Efecto de desenfoque */
    border: 1px solid rgba(255, 255, 255, 0.3); /* Opcional: añadir un borde sutil */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ajuste del sombreado */
    border-radius: 8px; /* Ajuste del borde redondeado */
}

/* Compatibilidad con navegadores */
@supports not ((-webkit-backdrop-filter: blur(4px)) or (backdrop-filter: blur(4px))) {
    .filtro {
        background-color: rgba(255, 255, 255, 0.7); /* Fallback para navegadores que no soportan backdrop-filter */
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.7); /* Ajuste del fondo fallback */
    }
}

</style>
# Gestión de Liga de Básquetbol

Aplicación web educativa para administrar equipos, jugadores y partidos de una liga de básquetbol.

## Tecnologías
- HTML5
- CSS3
- JavaScript
- Git y GitHub para el control de versiones y la publicación del código

**No utiliza Laravel ni MySQL.** Los datos se guardan en el almacenamiento local (`localStorage`) del navegador en el equipo donde se usa la aplicación.

## Cómo abrir el proyecto
1. Descarga y descomprime la carpeta del proyecto.
2. Abre la carpeta `liga_basket_github` desde Visual Studio Code.
3. Abre `index.html` en el navegador. Puedes usar la extensión Live Server de VS Code, o abrir el archivo directamente.
4. Registra al menos dos equipos para poder programar partidos.

## Funciones incluidas
- Panel resumen con contadores.
- Crear y eliminar equipos.
- Crear y eliminar jugadores, asignándolos a un equipo.
- Programar partidos con fecha y equipos local/visitante.
- Registrar y editar marcadores de partidos.
- Guardado local automático en el navegador.

## Git: primeros comandos
Abre la terminal de VS Code en esta carpeta y ejecuta:

```bash
git init
git add .
git commit -m "Primer commit: liga de basquetbol"
```

Si Git solicita identificarte, configura tu nombre y correo:

```bash
git config --global user.name "Tu nombre"
git config --global user.email "tu-correo@example.com"
```

## Subir a GitHub
1. Crea un repositorio vacío en https://github.com/new con el nombre `gestion-liga-basquetbol`.
2. No marques la opción de crear README si ya tienes este archivo.
3. Copia la URL HTTPS del repositorio y, en la terminal de esta carpeta, ejecuta (reemplaza la URL por la tuya):

```bash
git branch -M main
git remote add origin https://github.com/TU-USUARIO/gestion-liga-basquetbol.git
git push -u origin main
```

## Limitaciones
Esta versión es una demostración local: los datos no se sincronizan entre navegadores o computadoras, no hay inicio de sesión real ni base de datos compartida. Para una aplicación multiusuario se necesitaría un backend y almacenamiento centralizado.

SIGC — Sistema de Información para Gestión Comercial

> Objetivo general:
> Aplicación web desarrollada con PHP y MySQL para administrar los procesos esenciales de un negocio: control de clientes, proveedores, productos, compras y ventas.
> Incluye operaciones CRUD completas, diseño modular, autenticación de usuarios y funcionamiento en entorno local mediante XAMPP.

1) Estructura recomendada del proyecto

```
/SIGC
├─ /config
│   └─ db.php                  # Conexión a la base de datos (PDO)
├─ /modules
│   ├─ /clientes
│   │   ├─ create.php
│   │   ├─ read.php
│   │   ├─ update.php
│   │   └─ delete.php
│   ├─ /proveedores
│   │   ├─ create.php
│   │   ├─ read.php
│   │   ├─ update.php
│   │   └─ delete.php
│   ├─ /productos
│   │   ├─ create.php
│   │   ├─ read.php
│   │   ├─ update.php
│   │   └─ delete.php
│   ├─ /compras
│   │   ├─ create.php
│   │   ├─ read.php
│   │   ├─ update.php
│   │   └─ delete.php
│   └─ /ventas
│       ├─ create.php
│       ├─ read.php
│       ├─ update.php
│       └─ delete.php
├─
├─ /sql
│   ├─ ddl_sigc.sql            # Script de creación de base de datos y tablas
│   └─ erd_sigc.png            # Diagrama ERD (opcional)
├─ index.php
├─ login.php
├─ logout.php
└─ README.md
```
2) README.md
SIGC — Sistema de Información para Gestión Comercial
Descripción
SIGC es un sistema web desarrollado con PHP + MySQL (PDO) para gestionar de forma eficiente las áreas principales de un negocio:
Clientes, Proveedores, Productos, Compras y Ventas, cada uno con sus operaciones CRUD.

Tecnologías utilizadas

* PHP 7.4+ / PHP 8.x
* MySQL / MariaDB
* Apache (XAMPP)
* HTML5, CSS3, JavaScript
* Bootstrap 5 (opcional)
* Git y GitHub

Requisitos previos

* XAMPP instalado (Apache & MySQL activos).
* Git instalado (opcional).
* Editor de código (VSCode recomendado).
* Navegador moderno (Chrome, Edge, Firefox).

Instalación y ejecución

1. Clonar el repositorio

2. Copiar al servidor local

Ubica la carpeta dentro de:

```
C:\xampp\htdocs\sigc
```
3. Crear la base de datos

1. Abrir phpMyAdmin
2. Crear una BD llamada sigc_db o como se llama la base de datos crudphp (UTF8MB4)
3. Importar:

```
/sql/ddl_sigc.sql
```
ahi se encuentra la base de datos que solamente es importar

4. Ejecutar en navegador

```
http://localhost/SICG/
```
el usuario es :admin@mail.com
contraseña es: admin

5. Crear usuario administrador (opcional)

Puede hacerse desde phpMyAdmin o agregarse un script de inicialización.
Esto se crea desde la base de datos osea el usuario.





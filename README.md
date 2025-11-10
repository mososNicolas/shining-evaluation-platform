# 🌟 Shining Evaluation Platform

Sistema web para la evaluación del festival **"Shining Like a Star"**, desarrollado con **PHP**, **MySQL**, **Bootstrap** y **JavaScript**.  
Permite la gestión de evaluaciones por parte de jurados y el seguimiento de resultados por el rector.

---

## 🗂️ Estructura del proyecto

```bash
/includes       → Componentes comunes (header, footer, sidebar)
/dashboard      → Paneles dinámicos para cada tipo de usuario
/assets         → Archivos CSS, JS y recursos gráficos
/config         → Conexión a base de datos y variables globales
/index.php      → Página de inicio / login
```

---

## 👤 Tipos de usuario

- **Rector:** visualiza resultados generales, exporta puntajes y rankings.
- **Jurado de Inglés:** evalúa *pronunciación*, *fluidez* y *vocabulario*.
- **Jurado de Música:** evalúa *afinación*, *interpretación* y *proyección vocal*.

---

## 🧮 Cálculo de puntajes

El puntaje total ponderado se calcula de la siguiente manera:

- **Inglés:** 40% (promedio de 2 jurados)  
- **Música:** 35% (jurado único)  
- **Creatividad:** 25% (todos los jurados)

---

## 📂 Base de datos

**Nombre:** `shining_festival`  

Tablas principales:
- `participantes` → Información de los concursantes.  
- `jurados` → Datos y credenciales de acceso.  
- `evaluaciones` → Registros de puntuaciones individuales.  

---

## 🧑‍💻 Tecnologías utilizadas

- PHP 8+  
- MySQL 5.7+  
- Bootstrap 5  
- JavaScript (Chart.js para estadísticas)  

## ⚙️ Instalación

```bash
# Clonar el repositorio
git clone https://github.com/mososNicolas/shining-evaluation-platform.git
```

### Configurar entorno

1. Importar la base de datos `shining_festival.sql` en MySQL.  
2. Configurar credenciales de conexión en `config/database.php`.

### Ejecutar en servidor local

Abrir en el navegador:

```bash
http://localhost/Shining
```

# ✅ Informe de Pruebas del Proyecto AniValientes

**Fecha de cierre:** 10 de mayo de 2025  
**Responsable:** Jordi Hernández  
**Proyecto:** AniValientes – Plataforma contra el abandono animal

---

## 🔹 1. Autenticación de usuarios (API móvil)

**Test:** `MobileLoginTest`  
**Objetivo:** Verificar inicio de sesión desde la app móvil.  
**Resultados:**
- ✅ Inicio de sesión válido retorna token y datos.
- ✅ Credenciales inválidas son rechazadas.

---

## 🔹 2. Envío de denuncias desde la app

**Test:** `MobileReportTest`  
**Objetivo:** Crear denuncia con foto, geolocalización y descripción.  
**Resultados:**
- ✅ Requiere autenticación.
- ✅ Valida campos y subida de imagen.
- ✅ Test superado tras añadir extensión GD.

---

## 🔹 3. Consulta de denuncias propias

**Test:** `MobileReportListTest`  
**Objetivo:** Solo mostrar denuncias del usuario autenticado.  
**Resultado:** ✅ Funciona correctamente.

---

## 🔹 4. Acceso al panel web

**Test:** `DashboardAccessTest`  
**Objetivo:** Verificar acceso según rol.  
**Resultados:**
- ✅ Institucional: acceso permitido.
- ✅ Ciudadano: acceso denegado (403).
- ✅ Invitado: redirigido al login.

---

## 🔹 5. Actualización de denuncias (panel web)

**Test:** `ReportUpdateTest`  
**Objetivo:** Modificar estado, gravedad y respuesta.  
**Resultado:** ✅ Test ajustado al comportamiento real (redirect 303), validado con éxito.

---

## 🔹 6. Acceso a configuración institucional

**Test:** `SettingsAccessTest`  
**Resultado:** ✅ Acceso correctamente limitado a usuarios institucionales.

---

## 🔹 7. Subida de logotipo

**Test:** `LogoUploadTest`  
**Resultado:**
- ✅ Institucionales pueden subir logotipo.
- ✅ Ciudadanos no autorizados.

---

## 🔹 8. Listado de denuncias (panel institucional)

**Test:** `ReportListTest`  
**Resultados:**
- ✅ Institucionales ven las denuncias de su administración.
- ✅ Ciudadanos no pueden acceder.

---

## 🔹 9. Detalle de una denuncia

**Test:** `ReportDetailTest`  
**Resultados:**
- ✅ Institucionales acceden al detalle.
- ✅ Ciudadanos reciben error 403.

---

## 🔹 10. Dashboard API (estadísticas)

**Test:** `DashboardApiTest`  
**Resultado:**
- ✅ Devuelve correctamente:
  - Distribución de estados (`statusDistribution`)
  - Denuncias por día (`reportsByDay`)

---

## 🔹 11. API de perfil de usuario

**Test:** `UserProfileApiTest`  
**Resultado:** ✅ Devuelve datos completos del usuario autenticado.

---

## 🟡 Pendientes

- **Notificaciones de denuncia:** no implementadas, por tanto no testeadas.
- **Preferencias de configuración:** visuales por ahora, sin lógica funcional detrás.

---

## ✅ Conclusión

Se han testado correctamente:
- Funciones críticas de la **API móvil**
- Funcionalidad esencial del **panel web**
- Comprobaciones de **seguridad y roles**
- **Todos los tests actuales están superados** tras su depuración y adecuación.

---

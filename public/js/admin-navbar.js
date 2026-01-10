/**
 * SCMS Admin Navbar JavaScript
 * Sistema de navegación administrativo estilo WordPress
 */
(function() {
    'use strict';
    
    // Variables dinámicas que serán inyectadas desde PHP
    var BASE_URL = window.SCMS_BASE_URL || '';
    var CURRENT_URL = window.SCMS_CURRENT_URL || '';
    
    // Simple Toast Notification
    function showToast(message, type) {
        var toast = document.createElement('div');
        toast.className = 'scms-toast scms-toast-' + type;
        toast.textContent = message;
        toast.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:' + 
            (type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3') + 
            ';color:#fff;padding:12px 24px;border-radius:4px;z-index:999999;box-shadow:0 2px 8px rgba(0,0,0,0.3)';
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(function() { document.body.removeChild(toast); }, 300);
        }, 3000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Vanilla JS Dropdown Implementation
        var dropdownTriggers = document.querySelectorAll('#scms-wp-adminbar .dropdown-trigger');
        
        dropdownTriggers.forEach(function(trigger) {
            var targetId = trigger.getAttribute('data-target');
            var dropdown = document.getElementById(targetId);
            
            if (!dropdown) return;
            
            // Posicionar dropdown
            dropdown.style.position = 'absolute';
            dropdown.style.display = 'none';
            dropdown.style.zIndex = '999999';
            
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Cerrar otros dropdowns
                document.querySelectorAll('.dropdown-content').forEach(function(dd) {
                    if (dd !== dropdown) dd.style.display = 'none';
                });
                
                // Toggle este dropdown
                if (dropdown.style.display === 'none') {
                    // Mostrar temporalmente para medir
                    dropdown.style.display = 'block';
                    dropdown.style.visibility = 'hidden';
                    
                    var rect = trigger.getBoundingClientRect();
                    var dropdownWidth = dropdown.offsetWidth;
                    var viewportWidth = window.innerWidth;
                    
                    // Calcular posición Y (siempre debajo del trigger)
                    var topPos = rect.bottom;
                    dropdown.style.top = topPos + 'px';
                    
                    // Calcular posición X - detectar desbordamiento a la derecha
                    var leftPos = rect.left;
                    var rightEdge = leftPos + dropdownWidth;
                    
                    // Si se desborda a la derecha, alinear a la derecha del trigger
                    if (rightEdge > viewportWidth - 10) {
                        leftPos = rect.right - dropdownWidth;
                        // Si aún se desborda a la izquierda, ajustar
                        if (leftPos < 10) {
                            leftPos = 10;
                        }
                    }
                    
                    dropdown.style.left = leftPos + 'px';
                    dropdown.style.visibility = 'visible';
                } else {
                    dropdown.style.display = 'none';
                }
            });
        });
        
        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown-trigger') && !e.target.closest('.scms-toggle-switch')) {
                document.querySelectorAll('.dropdown-content').forEach(function(dd) {
                    dd.style.display = 'none';
                });
            }
        });
        
        // Cerrar dropdown al hacer clic en un item (excepto toggles)
        document.querySelectorAll('.dropdown-content a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // No cerrar si es parte de un toggle
                if (!e.target.closest('.scms-toggle-item')) {
                    document.querySelectorAll('.dropdown-content').forEach(function(dd) {
                        dd.style.display = 'none';
                    });
                }
            });
        });
        
        // Manejar toggles
        document.querySelectorAll('.scms-toggle-switch').forEach(function(toggleSwitch) {
            var checkbox = toggleSwitch.querySelector('input[type="checkbox"]');
            var action = toggleSwitch.getAttribute('data-action');
            var pageId = toggleSwitch.getAttribute('data-page-id');
            var formName = toggleSwitch.getAttribute('data-form-name');
            
            if (checkbox) {
                checkbox.addEventListener('change', function(e) {
                    e.stopPropagation();
                    var isChecked = this.checked;
                    
                    // Lógica para cada acción
                    switch(action) {
                        case 'toggle-visibility':
                            scmsAdminBar.togglePageVisibility(pageId, isChecked);
                            break;
                        case 'toggle-comments':
                            scmsAdminBar.toggleComments(pageId, isChecked);
                            break;
                        case 'toggle-notifications':
                            scmsAdminBar.toggleFormNotifications(formName, isChecked);
                            break;
                        case 'toggle-captcha':
                            scmsAdminBar.toggleFormCaptcha(formName, isChecked);
                            break;
                    }
                });
                
                // Prevenir que el click en el toggle cierre el dropdown
                toggleSwitch.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
        
        // Reposicionar dropdowns en scroll y resize
        window.addEventListener('scroll', function() {
            document.querySelectorAll('.dropdown-content').forEach(function(dd) {
                if (dd.style.display === 'block') {
                    dd.style.display = 'none';
                }
            });
        });
        
        window.addEventListener('resize', function() {
            document.querySelectorAll('.dropdown-content').forEach(function(dd) {
                if (dd.style.display === 'block') {
                    dd.style.display = 'none';
                }
            });
        });
        
        document.body.classList.add('scms-has-admin-bar');
        
        var fixedNavbar = document.querySelector('.navbar.fixed-top');
        if (fixedNavbar) {
            fixedNavbar.style.top = '46px';
        }
        
        var logoutLink = document.getElementById('scms-admin-bar-logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                    window.location.href = this.href;
                }
            });
        }
        
        scmsLoadNotifications();
    });
    
    function scmsLoadNotifications() {
        var badge = document.getElementById('scms-notification-count');
        if (badge) {
            // TODO: Implementar API de notificaciones
        }
    }
    
    function scmsExportFormData(formName) {
        if (!formName) return;
        window.location.href = BASE_URL + 'admin/siteforms/export/' + encodeURIComponent(formName);
        showToast('Descargando datos...', 'info');
    }
    
    function scmsTogglePageVisibility(pageId, isVisible) {
        if (!pageId) return;
        
        showToast(isVisible ? 'Publicando página...' : 'Ocultando página...', 'info');
        
        // Llamada AJAX a la API para cambiar estado
        var status = isVisible ? '1' : '2'; // 1 = published, 2 = draft
        var formData = new FormData();
        formData.append('status', status);
        
        fetch(BASE_URL + 'api/v1/pages/updatestatus/' + pageId, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.code === 200) {
                showToast(isVisible ? 'Página publicada exitosamente' : 'Página ocultada exitosamente', 'success');
            } else {
                showToast(data.error_message || 'Error al actualizar el estado', 'error');
                // Revertir el toggle si hay error
                var checkbox = document.querySelector('[data-page-id="' + pageId + '"][data-action="toggle-visibility"] input');
                if (checkbox) checkbox.checked = !isVisible;
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            showToast('Error al actualizar el estado de la página', 'error');
            // Revertir el toggle si hay error
            var checkbox = document.querySelector('[data-page-id="' + pageId + '"][data-action="toggle-visibility"] input');
            if (checkbox) checkbox.checked = !isVisible;
        });
    }
    
    function scmsToggleComments(pageId, enableComments) {
        if (!pageId) return;
        
        showToast(enableComments ? 'Habilitando comentarios...' : 'Deshabilitando comentarios...', 'info');
        
        // TODO: Implementar llamada AJAX
        console.log('Toggle comments:', pageId, enableComments);
    }
    
    function scmsToggleFormNotifications(formName, enableNotifications) {
        if (!formName) return;
        
        showToast(enableNotifications ? 'Habilitando notificaciones...' : 'Deshabilitando notificaciones...', 'info');
        
        // TODO: Implementar llamada AJAX
        console.log('Toggle form notifications:', formName, enableNotifications);
    }
    
    function scmsToggleFormCaptcha(formName, enableCaptcha) {
        if (!formName) return;
        
        showToast(enableCaptcha ? 'Habilitando CAPTCHA...' : 'Deshabilitando CAPTCHA...', 'info');
        
        // TODO: Implementar llamada AJAX
        console.log('Toggle form captcha:', formName, enableCaptcha);
    }
    
    function scmsDuplicatePage(pageId, pageTitle) {
        if (!pageId) return;
        
        if (confirm('¿Estás seguro de que deseas duplicar "' + pageTitle + '"?')) {
            showToast('Duplicando página...', 'info');
            
            // Usar la API para duplicar
            fetch(BASE_URL + 'api/v1/pages/duplicate/' + pageId, {
                method: 'POST',
                credentials: 'same-origin'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.code === 200 && data.data) {
                    showToast('Página duplicada exitosamente', 'success');
                    // Redirigir a editar la nueva página
                    setTimeout(function() {
                        window.location.href = BASE_URL + 'admin/pages/edit/' + data.data.page_id;
                    }, 1000);
                } else {
                    showToast(data.error_message || 'Error al duplicar la página', 'error');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showToast('Error al duplicar la página', 'error');
            });
        }
    }
    
    function scmsCopyPageUrl(url) {
        // Crear un elemento temporal para copiar al portapapeles
        var tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        
        try {
            document.execCommand('copy');
            showToast('Enlace copiado al portapapeles', 'success');
        } catch (err) {
            // Fallback para navegadores modernos
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast('Enlace copiado al portapapeles', 'success');
                }).catch(function() {
                    showToast('Error al copiar el enlace', 'error');
                });
            } else {
                showToast('Error al copiar el enlace', 'error');
            }
        } finally {
            document.body.removeChild(tempInput);
        }
    }
    
    function scmsArchivePage(pageId, pageTitle) {
        if (!pageId) return;
        
        if (confirm('¿Estás seguro de que deseas archivar "' + pageTitle + '"?\n\nLa página no será visible pero podrás recuperarla más tarde.')) {
            showToast('Archivando página...', 'info');
            
            // Enviar status = 3 (archived) a través de la API
            var formData = new FormData();
            formData.append('status', '3');
            
            fetch(BASE_URL + 'api/v1/pages/updatestatus/' + pageId, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.code === 200) {
                    showToast('Página archivada exitosamente', 'success');
                    // Redirigir al listado de páginas
                    setTimeout(function() {
                        window.location.href = BASE_URL + 'admin/pages';
                    }, 1000);
                } else {
                    showToast(data.error_message || 'Error al archivar la página', 'error');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showToast('Error al archivar la página', 'error');
            });
        }
    }
    
    // API pública
    window.scmsAdminBar = {
        loadNotifications: scmsLoadNotifications,
        exportFormData: scmsExportFormData,
        togglePageVisibility: scmsTogglePageVisibility,
        toggleComments: scmsToggleComments,
        toggleFormNotifications: scmsToggleFormNotifications,
        toggleFormCaptcha: scmsToggleFormCaptcha,
        duplicatePage: scmsDuplicatePage,
        copyPageUrl: scmsCopyPageUrl,
        archivePage: scmsArchivePage
    };
})();

/**
 * SCMS Admin Navbar JavaScript
 * Sistema de navegación administrativo estilo WordPress
 */
(function () {
    "use strict";

    var BASE_URL = window.SCMS_BASE_URL || "";
    var CURRENT_URL = window.SCMS_CURRENT_URL || "";
    var pollTimer = null;

    function scmsLang(key) {
        var dict = window.SCMS_LANG || {};
        return dict[key] ? dict[key] : key;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function notificationHref(url) {
        if (!url) {
            return "#";
        }
        url = String(url).replace(/^\//, "");
        if (/^(javascript|data):/i.test(url)) {
            return "#";
        }
        return BASE_URL + url;
    }

    function showToast(message, type) {
        var toast = document.createElement("div");
        toast.className = "scms-toast scms-toast-" + type;
        toast.textContent = message;
        toast.style.cssText =
            "position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:" +
            (type === "success" ? "#4caf50" : type === "error" ? "#f44336" : "#2196f3") +
            ";color:#fff;padding:12px 24px;border-radius:4px;z-index:999999;box-shadow:0 2px 8px rgba(0,0,0,0.3)";
        document.body.appendChild(toast);
        setTimeout(function () {
            toast.style.opacity = "0";
            toast.style.transition = "opacity 0.3s";
            setTimeout(function () {
                if (toast.parentNode) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    function updateBadge(count) {
        var badge = document.getElementById("scms-notification-count");
        if (!badge) {
            return;
        }
        if (count > 0) {
            badge.textContent = count > 99 ? "99+" : String(count);
            badge.style.display = "";
        } else {
            badge.textContent = "0";
            badge.style.display = "none";
        }
    }

    function renderNotifications(items) {
        var list = document.getElementById("scms-notifications-dropdown");
        if (!list) {
            return;
        }
        var header = list.querySelector(".scms-dropdown-header");
        var viewAll = list.querySelector('a[href*="admin/notifications"]');
        var html = "";
        if (header) {
            html += header.outerHTML;
        }
        html += '<li class="divider scms-adminbar-divider"></li>';
        if (!items.length) {
            html +=
                '<li id="scms-no-notifications"><a href="#!"><i class="material-icons scms-adminbar-icon">info</i>' +
                scmsLang("notifications_empty") +
                "</a></li>";
        } else {
            items.forEach(function (item) {
                var title = escapeHtml(item.title || "");
                var url = notificationHref(item.url);
                html +=
                    '<li class="scms-notification-item" data-id="' +
                    escapeHtml(item.notification_id) +
                    '"><a href="' +
                    escapeHtml(url) +
                    '"><i class="material-icons scms-adminbar-icon">notifications</i>' +
                    title +
                    "</a></li>";
            });
        }
        html += '<li class="divider scms-adminbar-divider"></li>';
        if (viewAll) {
            html += "<li>" + viewAll.outerHTML + "</li>";
        } else {
            html +=
                '<li><a href="' +
                BASE_URL +
                'admin/notifications"><i class="material-icons scms-adminbar-icon">list</i>' +
                scmsLang("notifications_view_all") +
                "</a></li>";
        }
        list.innerHTML = html;

        list.querySelectorAll(".scms-notification-item a").forEach(function (link) {
            link.addEventListener("click", function (e) {
                var li = this.closest(".scms-notification-item");
                if (!li) {
                    return;
                }
                e.preventDefault();
                var id = li.getAttribute("data-id");
                var href = this.getAttribute("href");
                markReadThenGo(id, href);
            });
        });
    }

    function markReadThenGo(id, href) {
        fetch(BASE_URL + "api/v1/notifications/read/" + id, {
            method: "POST",
            credentials: "same-origin",
        })
            .then(function (response) {
                return response.json();
            })
            .then(function () {
                if (href && href !== "#") {
                    window.location.href = href;
                } else {
                    scmsLoadNotifications();
                }
            })
            .catch(function () {
                if (href && href !== "#") {
                    window.location.href = href;
                }
            });
    }

    function scmsLoadNotifications() {
        fetch(BASE_URL + "api/v1/notifications?status=1", {
            credentials: "same-origin",
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                var items =
                    data && data.code == 200 && data.data ? data.data : [];
                if (!Array.isArray(items)) {
                    items = [];
                }
                renderNotifications(items);
                updateBadge(items.length);
            })
            .catch(function () {
                updateBadge(0);
            });
    }

    function startPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
        }
        pollTimer = setInterval(function () {
            if (!document.hidden) {
                scmsLoadNotifications();
            }
        }, 45000);
        document.addEventListener("visibilitychange", function () {
            if (!document.hidden) {
                scmsLoadNotifications();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        var dropdownTriggers = document.querySelectorAll(
            "#scms-wp-adminbar .dropdown-trigger"
        );

        dropdownTriggers.forEach(function (trigger) {
            var targetId = trigger.getAttribute("data-target");
            var dropdown = document.getElementById(targetId);

            if (!dropdown) return;

            dropdown.style.position = "absolute";
            dropdown.style.display = "none";
            dropdown.style.zIndex = "999999";

            trigger.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();

                document.querySelectorAll(".dropdown-content").forEach(function (dd) {
                    if (dd !== dropdown) dd.style.display = "none";
                });

                if (dropdown.style.display === "none") {
                    dropdown.style.display = "block";
                    dropdown.style.visibility = "hidden";

                    var rect = trigger.getBoundingClientRect();
                    var dropdownWidth = dropdown.offsetWidth;
                    var viewportWidth = window.innerWidth;

                    var topPos = rect.bottom;
                    dropdown.style.top = topPos + "px";

                    var leftPos = rect.left;
                    var rightEdge = leftPos + dropdownWidth;

                    if (rightEdge > viewportWidth - 10) {
                        leftPos = rect.right - dropdownWidth;
                        if (leftPos < 10) {
                            leftPos = 10;
                        }
                    }

                    dropdown.style.left = leftPos + "px";
                    dropdown.style.visibility = "visible";
                } else {
                    dropdown.style.display = "none";
                }
            });
        });

        document.addEventListener("click", function (e) {
            if (
                !e.target.closest(".dropdown-trigger") &&
                !e.target.closest(".scms-toggle-switch")
            ) {
                document.querySelectorAll(".dropdown-content").forEach(function (dd) {
                    dd.style.display = "none";
                });
            }
        });

        document.querySelectorAll(".dropdown-content a").forEach(function (link) {
            link.addEventListener("click", function (e) {
                if (!e.target.closest(".scms-toggle-item")) {
                    document.querySelectorAll(".dropdown-content").forEach(function (dd) {
                        dd.style.display = "none";
                    });
                }
            });
        });

        document.querySelectorAll(".scms-toggle-switch").forEach(function (toggleSwitch) {
            var checkbox = toggleSwitch.querySelector('input[type="checkbox"]');
            var action = toggleSwitch.getAttribute("data-action");
            var pageId = toggleSwitch.getAttribute("data-page-id");
            var formName = toggleSwitch.getAttribute("data-form-name");

            if (checkbox) {
                checkbox.addEventListener("change", function (e) {
                    e.stopPropagation();
                    var isChecked = this.checked;

                    switch (action) {
                        case "toggle-visibility":
                            scmsAdminBar.togglePageVisibility(pageId, isChecked);
                            break;
                        case "toggle-comments":
                            scmsAdminBar.toggleComments(pageId, isChecked);
                            break;
                        case "toggle-notifications":
                            scmsAdminBar.toggleFormNotifications(formName, isChecked);
                            break;
                        case "toggle-captcha":
                            scmsAdminBar.toggleFormCaptcha(formName, isChecked);
                            break;
                    }
                });

                toggleSwitch.addEventListener("click", function (e) {
                    e.stopPropagation();
                });
            }
        });

        window.addEventListener("scroll", function () {
            document.querySelectorAll(".dropdown-content").forEach(function (dd) {
                if (dd.style.display === "block") {
                    dd.style.display = "none";
                }
            });
        });

        window.addEventListener("resize", function () {
            document.querySelectorAll(".dropdown-content").forEach(function (dd) {
                if (dd.style.display === "block") {
                    dd.style.display = "none";
                }
            });
        });

        document.body.classList.add("scms-has-admin-bar");

        var fixedNavbar = document.querySelector(".navbar.fixed-top");
        if (fixedNavbar) {
            fixedNavbar.style.top = "46px";
        }

        var logoutLink = document.getElementById("scms-admin-bar-logout");
        if (logoutLink) {
            logoutLink.addEventListener("click", function (e) {
                e.preventDefault();
                if (confirm("¿Estás seguro de que deseas cerrar sesión?")) {
                    window.location.href = this.href;
                }
            });
        }

        scmsLoadNotifications();
        startPolling();
    });

    function scmsExportFormData(formName) {
        if (!formName) return;
        window.location.href =
            BASE_URL + "admin/siteforms/export/" + encodeURIComponent(formName);
        showToast("Descargando datos...", "info");
    }

    function scmsTogglePageVisibility(pageId, isVisible) {
        if (!pageId) return;

        showToast(isVisible ? "Publicando página..." : "Ocultando página...", "info");

        var status = isVisible ? "1" : "2";
        var formData = new FormData();
        formData.append("status", status);

        fetch(BASE_URL + "api/v1/pages/updatestatus/" + pageId, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.code === 200) {
                    showToast(
                        isVisible
                            ? "Página publicada exitosamente"
                            : "Página ocultada exitosamente",
                        "success"
                    );
                } else {
                    showToast(
                        data.error_message || "Error al actualizar el estado",
                        "error"
                    );
                    var checkbox = document.querySelector(
                        '[data-page-id="' +
                            pageId +
                            '"][data-action="toggle-visibility"] input'
                    );
                    if (checkbox) checkbox.checked = !isVisible;
                }
            })
            .catch(function () {
                showToast("Error al actualizar el estado de la página", "error");
                var checkbox = document.querySelector(
                    '[data-page-id="' +
                        pageId +
                        '"][data-action="toggle-visibility"] input'
                );
                if (checkbox) checkbox.checked = !isVisible;
            });
    }

    function scmsToggleComments(pageId, enableComments) {
        if (!pageId) return;
        showToast(
            enableComments ? "Habilitando comentarios..." : "Deshabilitando comentarios...",
            "info"
        );
    }

    function scmsToggleFormNotifications(formName, enableNotifications) {
        if (!formName) return;

        var formData = new FormData();
        formData.append("name", formName);
        formData.append("notify", enableNotifications ? "1" : "0");

        fetch(BASE_URL + "api/v1/siteforms/notify", {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.code === 200) {
                    showToast(
                        enableNotifications
                            ? scmsLang("notifications_enabled")
                            : scmsLang("notifications_disabled"),
                        "success"
                    );
                } else {
                    showToast(data.error_message || scmsLang("toast_error"), "error");
                    var checkbox = document.querySelector(
                        '[data-form-name="' +
                            formName +
                            '"][data-action="toggle-notifications"] input'
                    );
                    if (checkbox) checkbox.checked = !enableNotifications;
                }
            })
            .catch(function () {
                showToast(scmsLang("toast_error"), "error");
                var checkbox = document.querySelector(
                    '[data-form-name="' +
                        formName +
                        '"][data-action="toggle-notifications"] input'
                );
                if (checkbox) checkbox.checked = !enableNotifications;
            });
    }

    function scmsToggleFormCaptcha(formName, enableCaptcha) {
        if (!formName) return;
        showToast(
            enableCaptcha ? "Habilitando CAPTCHA..." : "Deshabilitando CAPTCHA...",
            "info"
        );
    }

    function scmsDuplicatePage(pageId, pageTitle) {
        if (!pageId) return;

        if (confirm('¿Estás seguro de que deseas duplicar "' + pageTitle + '"?')) {
            showToast("Duplicando página...", "info");

            fetch(BASE_URL + "api/v1/pages/duplicate/" + pageId, {
                method: "POST",
                credentials: "same-origin",
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.code === 200 && data.data) {
                        showToast("Página duplicada exitosamente", "success");
                        setTimeout(function () {
                            window.location.href =
                                BASE_URL + "admin/pages/edit/" + data.data.page_id;
                        }, 1000);
                    } else {
                        showToast(
                            data.error_message || "Error al duplicar la página",
                            "error"
                        );
                    }
                })
                .catch(function () {
                    showToast("Error al duplicar la página", "error");
                });
        }
    }

    function scmsCopyPageUrl(url) {
        var tempInput = document.createElement("input");
        tempInput.value = url || CURRENT_URL;
        document.body.appendChild(tempInput);
        tempInput.select();

        try {
            document.execCommand("copy");
            showToast("Enlace copiado al portapapeles", "success");
        } catch (err) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url || CURRENT_URL).then(
                    function () {
                        showToast("Enlace copiado al portapapeles", "success");
                    },
                    function () {
                        showToast("Error al copiar el enlace", "error");
                    }
                );
            } else {
                showToast("Error al copiar el enlace", "error");
            }
        } finally {
            document.body.removeChild(tempInput);
        }
    }

    function scmsArchivePage(pageId, pageTitle) {
        if (!pageId) return;

        if (
            confirm(
                '¿Estás seguro de que deseas archivar "' +
                    pageTitle +
                    '"?\n\nLa página no será visible pero podrás recuperarla más tarde.'
            )
        ) {
            showToast("Archivando página...", "info");

            var formData = new FormData();
            formData.append("status", "3");

            fetch(BASE_URL + "api/v1/pages/updatestatus/" + pageId, {
                method: "POST",
                body: formData,
                credentials: "same-origin",
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.code === 200) {
                        showToast("Página archivada exitosamente", "success");
                        setTimeout(function () {
                            window.location.href = BASE_URL + "admin/pages";
                        }, 1000);
                    } else {
                        showToast(
                            data.error_message || "Error al archivar la página",
                            "error"
                        );
                    }
                })
                .catch(function () {
                    showToast("Error al archivar la página", "error");
                });
        }
    }

    window.scmsAdminBar = {
        loadNotifications: scmsLoadNotifications,
        exportFormData: scmsExportFormData,
        togglePageVisibility: scmsTogglePageVisibility,
        toggleComments: scmsToggleComments,
        toggleFormNotifications: scmsToggleFormNotifications,
        toggleFormCaptcha: scmsToggleFormCaptcha,
        duplicatePage: scmsDuplicatePage,
        copyPageUrl: scmsCopyPageUrl,
        archivePage: scmsArchivePage,
    };
})();

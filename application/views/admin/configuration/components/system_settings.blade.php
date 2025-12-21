<div v-show="sectionActive == 'system'" class="col s12">
    <div class="row">
        <div class="col s12">
            <h5 class="widget-title">
                <i class="material-icons left">settings_applications</i> Mantenimiento del Sistema
            </h5>
            <p class="grey-text">Configura la retención de logs y las tareas de limpieza automática.</p>
        </div>
    </div>

    <div class="row" v-if="systemConfigurations.length > 0">
        <div class="col s12">
            <configuration v-for="configuration in systemConfigurations" :key="configuration.site_config_id" :configuration="configuration"></configuration>
        </div>
    </div>
    
    <div class="row" v-else>
        <div class="col s12 center">
            <div class="preloader-wrapper small active">
                <div class="spinner-layer spinner-blue-only">
                  <div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div>
                </div>
            </div>
            <p class="grey-text">Cargando configuraciones de sistema...</p>
        </div>
    </div>
    
    <div class="row" v-if="lastCleanupResult">
        <div class="col s12">
            <div class="card-panel green lighten-5">
                <span class="green-text text-darken-2">
                    <i class="material-icons left">check_circle</i>
                    <strong>Último mantenimiento realizado:</strong> 
                    Se eliminaron @{{lastCleanupResult.system_logs}} logs de sistema, 
                    @{{lastCleanupResult.api_logs}} logs de API y 
                    @{{lastCleanupResult.user_tracking}} tracks de usuarios.
                </span>
            </div>
        </div>
    </div>
</div>

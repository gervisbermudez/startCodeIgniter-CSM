<script type="text/x-template" id="FormSiteDetails-template">
    <div id="FormSiteDetails-root" class="container">
        <div class="col s12 center" v-bind:class="{ hide: !loader }">
            <br><br>
            <preloader />
        </div>
        <div class="form" v-cloak v-if="!loader">
            <div class="row">
                <div class="col s12">
                    <ul class="tabs" id="formTabs">
                        <li class="tab col s3"><a class="active" href="#test1"><i class="material-icons">assignment</i>
                                Detalles</a></li>
                        <li v-if="data.user_tracking_id" class="tab col s3"><a href="#test2"><i class="material-icons">assignment_ind</i> Tracking</a>
                        </li>
                        <li class="tab col s3"><a href="#test3"><i class="material-icons">description</i> Formulario</a>
                        </li>
                    </ul>
                </div>
                <div id="test1" class="row">
                    <div class="col s8">
                        <div class="col s12" v-for="(key, index) in keys" :key="index">
                            <b>@{{key | capitalize}}:</b> <br />
                            <div>
                                @{{data.siteform_submit_data[key]}}
                            </div>
                        <br />
                        </div>
                        <div class="row" v-cloak v-if="!loader">
                            <div class="col s12">
                                <br />
                                <br />
                                <a v-if="data.status == 1" v-on:click="setArchive" class="waves-effect waves-light btn"><i
                                        class="material-icons left">assignment_turned_in</i>Marcar como Visto</a>
                                <a v-if="data.status == 2" class="waves-effect waves-light btn disabled"><i
                                    class="material-icons left">check</i>Visto</a>
                            </div>
                        </div>
                    </div>
                    <div class="col s4">
                        <div class="col s12">
                            <b>Formulario:</b> <br />
                            <div>
                                @{{data.siteform.name}}
                            </div>
                            <br />
                        </div>
                        <div class="col s12">
                            <b>date_create</b> <br />
                            <div>
                                @{{timeAgo(data.date_create)}}
                            </div>
                            <br />
                        </div>
                    </div>
                </div>
                <div id="test2" class="row" v-if="data.user_tracking_id">
                    <div class="col s12">
                        <b>client ip:</b> <br />
                        <div>
                            @{{data.user_tracking.client_ip}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>date_create</b> <br />
                        <div>
                            @{{timeAgo(data.user_tracking.date_create)}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>no_of_visits</b> <br />
                        <div>
                            @{{data.user_tracking.no_of_visits}}
                        </div>
                        <br />
                    </div>
                    <div class="col s12">
                        <b>user_agent</b> <br />
                        <div>
                            @{{data.user_tracking.user_agent}}
                        </div>
                        <br />
                    </div>
                </div>
                <div class="row" id="test3">

                            <div class="col s12">
                                <b>Nombre:</b> <br />
                                <div>
                                    @{{data.siteform.name}}
                                </div>
                                <br />
                            </div>
                            <div class="col s12">
                                <b>Fecha creacion:</b> <br />
                                <div>
                                    @{{timeAgo(data.siteform.date_create)}}
                                </div>
                                <br />
                            </div>
                            <div class="col s12">
                                <b>Template:</b> <br />
                                <div>
                                    @{{data.siteform.template}}
                                </div>
                                <br />
                            </div>
                            <div class="col s12">
                                <b>Estado:</b> <br />
                                <div>
                                    @{{data.siteform.status ? "Activo" : "Inactivo"}}
                                </div>
                            <div class="col s12">
                                <p><b>Creado por</b>:</p>
                                <userInfo v-bind:user="user" />
                                <br /><br />
                            </div>
                            <div class="col s12">
                                <a :href="base_url('admin/siteforms/editar/' + data.siteform.siteform_id)" class="waves-effect waves-light btn"><i class="material-icons left">edit</i>Editar Formulario</a>
                            </div>
                            <br /><br />

                </div>
            </div>
        </div>
    </div>
    <div class="row" v-cloak v-if="!loader">
        <div class="col s12">
            <br />
            <br />
            <a v-on:click="back" class="waves-effect waves-light btn"><i
                    class="material-icons left">arrow_back</i>Ver Listado</a>
        </div>
    </div>
</div>
</script>
<style>
.form {
    padding: 10px;
    width: 95%;
    background-color: #fff;
    margin-top: 20px;
    border-radius: 5px;
    border: solid 1px #ccc;
    overflow: hidden;
    position: relative;
}
</style>

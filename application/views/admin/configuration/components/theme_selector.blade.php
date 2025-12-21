        <div v-show="sectionActive == 'theme'" class="container form">
            <div class="row">
                <div class="col s12">
                    <h4>Manage Themes</h4>
                </div>
            </div>
            <div class="row pages">
                <div class="col s12">
                    <div class="col s12 m4" v-for="(theme, index) in themes" :key="index">
                        <div class="card page-card">
                            <div class="card-image">
                                <div class="card-image-container">
                                    <img :src="getThemePreviewUrl(index, theme)" />
                                </div>
                                <label class="indicator">
                                    <input name="group1" type="radio" :checked="getThemeIsChecked(index)"
                                        v-on:change="changeTheme(index)" />
                                    <span>&nbsp;</span>
                                </label>
                            </div>
                            <div class="card-content">
                                <div>
                                    <span class="card-title">@{{theme.name}}
                                        <a href="#!"><span class="activator right"><i
                                                    class="material-icons">more_vert</i></span></a>
                                    </span>
                                    <div class="card-info">
                                        <p>
                                            @{{theme.description}}
                                        </p>
                                        <a @click="changeTheme(index)" class="waves-effect waves-light btn "><i
                                                class="material-icons left">brush</i>Aplicar theme</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-reveal">
                                <span class="card-title grey-text text-darken-4">
                                    <i class="material-icons right">close</i>
                                    @{{theme.name}}
                                </span>
                                <span class="subtitle">
                                    @{{theme.description}}
                                </span>
                                <ul>
                                    <li><b>Author:</b> <br> @{{theme.author}}</li>
                                    <li><b>Actualizacion:</b> <br> @{{theme.updated}}</li>
                                    <li><b>License:</b> @{{theme.license}}</li>
                                    <li><b>Url:</b> @{{theme.url}}</li>
                                    <li><b>Url:</b> @{{theme.url}}</li>
                                    <li><b>Version:</b> @{{theme.version}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

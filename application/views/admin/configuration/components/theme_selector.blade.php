        <div v-show="sectionActive == 'theme'" class="container form">
            <div class="config-section-header">
                <h2 class="page-header">{{ lang('config_manage_themes') }}</h2>
            </div>
            <div class="row pages">
                <div class="col s12 m4" v-for="(theme, index) in themes" :key="index">
                    <div class="card z-depth-1 theme-card">
                        <div class="card-image">
                            <div class="card-image-container">
                                <img :src="getThemePreviewUrl(index, theme)" :alt="theme.name" />
                            </div>
                            <label class="indicator">
                                <input name="theme-group" type="radio" :checked="getThemeIsChecked(index)"
                                    v-on:change="changeTheme(index)" />
                                <span>&nbsp;</span>
                            </label>
                        </div>
                        <div class="card-content">
                            <span class="card-title">
                                @{{theme.name}}
                                <a href="#!" class="activator right" aria-label="{{ lang('options') }}"><i class="material-icons">more_vert</i></a>
                            </span>
                            <p>@{{theme.description}}</p>
                            <span class="chip theme-active-chip" v-if="getThemeIsChecked(index)">{{ lang('config_theme_active') }}</span>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4">
                                <i class="material-icons right">close</i>
                                @{{theme.name}}
                            </span>
                            <ul>
                                <li><b>{{ lang('config_theme_author') }}:</b> @{{theme.author}}</li>
                                <li><b>{{ lang('config_theme_updated') }}:</b> @{{theme.updated}}</li>
                                <li><b>{{ lang('config_theme_license') }}:</b> @{{theme.license}}</li>
                                <li><b>{{ lang('config_theme_url') }}:</b> @{{theme.url}}</li>
                                <li><b>{{ lang('config_theme_version') }}:</b> @{{theme.version}}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script type="text/x-template" id="data-selector">
<div :id="modal" class="modal modal-fixed-footer data-selector">
    <div class="modal-content">
    <div class="row">
    <div class="col s12">
      <ul class="tabs" id="selectorTabs">
        <li class="tab col s3" v-for="(model, index) in models" :key="index">
            <a  class="active" :href="'#selector' + model" @click="destroyFileinputInstance();">
                <i class="material-icons">folder_open</i> @{{model}}
            </a>
        </li>
      </ul>
    </div>
    <div id="#selectorpages" class="col s12">
        <div class="pages" v-cloak v-if="pages.length > 0">
            <div class="row">
                <div class="col s12 m4" v-for="(page, index) in pages" :key="index">
                    <div class="card page-card">
                        <div class="card-image">
                            <div class="card-image-container">
                                <img :src="getPageImagePath(page)" />
                            </div>
                            <label class="checkbox">
                                <input type="checkbox" class="filled-in" v-model="page.selected" v-if="showCheckbox(page)"/>
                                <span>&nbsp;</span>
                            </label>
                        </div>
                        <div class="card-content">
                            <div>
                                <span class="card-title"><a :href="base_url(page.path)" target="_blank">@{{page.title}}</a> <i v-if="page.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('public'); ?>">public</i>
                                    <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('private'); ?>">lock</i>
                                </span>
                                <div class="card-info">
                                    <p>
                                        @{{getPageContentText(page)}}
                                    </p>
                                    <span class="activator right"><i class="material-icons">more_vert</i></span>
                                    <user-info :user="page.user" />
                                </div>
                            </div>
                        </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4">
                                <i class="material-icons right">close</i>
                                @{{page.title}}
                            </span>
                            <span class="subtitle">
                                @{{page.subtitle}}
                            </span>
                            <ul>
                                <li><b><?php echo lang('publish_date'); ?>:</b> <br> @{{page.date_publish ? page.date_publish : page.date_create}}</li>
                                <li><b><?php echo lang('category'); ?>:</b> @{{page.categorie}}</li>
                                <li><b><?php echo lang('subcategory'); ?>:</b> @{{page.subcategorie ? page.subcategorie : '<?php echo lang('none'); ?>'}}</li>
                                <li><b><?php echo lang('template'); ?>:</b> @{{page.template}}</li>
                                <li><b><?php echo lang('type'); ?>:</b> @{{page.page_type_name}}</li>
                                <li><b><?php echo lang('status'); ?>:</b>
                                    <span v-if="page.status == 1">
                                        <?php echo lang('published'); ?>
                                    </span>
                                    <span v-else>
                                        <?php echo lang('draft'); ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat"><?php echo lang('cancel'); ?></a>
        <button class="btn waves-effect waves-light" type="submit" v-on:click="onClickButton()" name="action">
            <?php echo lang('select'); ?>
            <i class="material-icons right">send</i>
        </button>
    </div>
</div>
</script>

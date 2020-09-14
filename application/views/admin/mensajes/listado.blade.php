@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="mensaje-container">
    <div class="row">
        <div class="col s12 search-bar">
            <nav class="red darken-1">
                <span class="current-folder"><?php echo $folder; ?></span>
                <span class="options"><i class="material-icons">more_vert</i></span>
                <div class="nav-wrapper">
                    <form>
                        <div class="input-field">
                            <input id="search" type="search" placeholder="Buscar...">
                            <label for="search"><i class="material-icons">search</i></label>
                            <i class="material-icons">close</i>
                        </div>
                    </form>
                </div>

            </nav>
        </div>
        <div class="col m1 s2 folders">
            <a href="#" class="tooltipped  folder" data-folder="1" data-position="right" data-delay="50" data-tooltip="Inbox"><i class="material-icons">inbox</i></a>
            <a href="#" class="tooltipped folder" data-folder="2" data-position="right" data-delay="50" data-tooltip="Archived"><i class="material-icons">archive</i></a>
            <a href="#" class="tooltipped folder" data-folder="3" data-position="right" data-delay="50" data-tooltip="Send"><i class="material-icons">send</i></a>
            <a href="#" class="tooltipped folder" data-folder="7" data-position="right" data-delay="50" data-tooltip="Drafts"><i class="material-icons">drafts</i></a>
            <a href="#" class="tooltipped folder" data-folder="6" data-position="right" data-delay="50" data-tooltip="Starred"><i class="material-icons">grade</i></a>
            <a href="#" class="tooltipped folder" data-folder="4" data-position="right" data-delay="50" data-tooltip="Deleted"><i class="material-icons">delete</i></a>
            <a href="#" class="tooltipped folder" data-folder="5" data-position="right" data-delay="50" data-tooltip="Spam"><i class="material-icons">report</i></a>
        </div>
        <div class="col m5 s10 l4" id="list-folder">
            <div class="collection" data-active-folder="Inbox">
                <?php echo $list ?>
            </div>
        </div>
        <div class="col s12 m6 l7 card" id="mail-conten">
            <div class="card-content" data-id="<?php echo $mensajes[0]['id'] ?>">
                <div class="preview">
                    <?php if (isset($preview)): ?>
                    <?php echo $preview ?>
                    <?php endif ?>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;"><a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="Nuevo Mensaje" href="#" id="mail-create"><i class="large material-icons">add</i></a></div>
<div class="compose-container">
    <div class="compose-msg-cont hidden">
        <div class="compose-msg-head blue darken-1 white-text">
            <span class="window-title">Nuevo Mensaje</span>
            <a href="#" class="white-text right close-compose-msg"><i class="material-icons">close</i></a>
            <a href="#" class="white-text right toggle-compose-msg"><i class="material-icons">expand_more</i></a>
        </div>
        <div class="compose-msg-body">
            <div class="chips"></div>
            <input id="subject" type="text" class="validate" placeholder="Subject">
            <textarea id="mensaje" class="materialize-textarea" placeholder="Mensaje"></textarea>
            <button class="btn waves-effect waves-light" type="submit" name="action">Enviar
                <i class="material-icons right">send</i>
            </button>
        </div>
    </div>
</div>
@endsection

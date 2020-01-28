@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container" id="formModule">
    <div class="row" v-cloak>
        <div class="col s9">
            <div class="row">
                <div class="col s12">
                  <ul class="vtabs">
                    <li class="vtab col s3" v-for="(tab, index) in tabs" :id="index" :class="{active : tab.active}">
                      <a :href="'#' + tab.tabID" @click="setActive(index)" v-if="!tab.edited">@{{tab.name}}</a>
                      <i class="material-icons right" v-if="!tab.edited && index != 0" @click="deleteTab(index)">delete</i>
                      <input type="text" :id="'input' + index" v-model="tab.name" v-on:keyup.enter="saveTab(index)" v-on:blur="saveTab(index)" v-if="tab.edited" >
                    </li>
                    <li class="vtab col s3"><a href="#tab1" @click="addTab()">New Tab +</a></li>
                  </ul>
                </div>
              </div>
              <div class="col s12 tab-pane" v-for="(tab, i) in tabs" :id="tab.tabID" :class="{active : tab.active}">
                  <div id="simple-list">
                      <div class="row" v-for="(field, index) in tab.fields">
                          <div class="col s12 component">
                            <component v-bind:is="field.component" :tab-parent="tab" :field-ref-index="index" :field-ref="field" ref="field.component"></component>
                          </div>
                      </div>
                  </div>
              </div>
        </div>
        <div class="col s3 formsElements">
            <div class="row">
                <div class="col s12">
                    <ul class="collection with-header">
                        <li class="collection-header"><h5>Campos</h5></li>
                        <li class="collection-item" v-for="(formsElement, index) in formsElements">
                            <div>@{{formsElement.displayName}} 
                            <a href="#!" class="secondary-content" @click="addField(formsElement)"><i class="material-icons">@{{formsElement.icon}}</i></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col s12 text-center">
            <a class="waves-effect waves-light btn" @click="saveData()"><i class="material-icons right">cloud</i> Guardar</a>
        </div>
    </div>
</div>
<script type="text/x-template" id="formFieldTitle-template">
    <div class="row formFieldTitle">

        <div class="input-field col s12"> <b>Field Preview:</b> <br/>
            <input :placeholder="fieldPlaceholder" :id="fieldID" type="text" class="validate">
            <label :for="fieldID">@{{fieldName}}</label>
        </div>
        <div class="col s12">
            <ul class="collapsible">
                <li>
                  <div class="collapsible-header"><i class="material-icons">settings</i>Config</div>
                  <div class="collapsible-body">
                    <div class="row">
                        <div class="input-field col s12">
                            Field Name
                            <br />
                            <input placeholder="Field Name" @keyup="convertfielApiID()" v-model="fieldName" type="text" class="validate">
                        </div>
                        <div class="input-field col s12">
                            Api ID
                            <input placeholder="Api ID" v-model="fielApiID" type="text" class="validate">
                        </div>
                        <div class="input-field col s12">
                            Placeholder
                            <input placeholder="Field Placeholder" v-model="fieldPlaceholder" type="text" class="validate">
                        </div>
                    </div>
                  </div>
                </li>
              </ul>
        </div>
    </div>
</script>
@endsection


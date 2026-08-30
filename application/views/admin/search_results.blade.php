@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div id="root" class="search-results-page">
    <div class="col s12 center" v-show="loader">
        <br><br>
        <preloader></preloader>
    </div>
    <div v-cloak v-show="!loader">
        <div class="page-header">{{ lang('search_results_title') }}</div>
        <p class="search-results-count" v-if="searchTerm">@{{ resultsCountLabel }}</p>
        <div class="row search-results-toolbar">
            <div class="input-field col s12 search-results-input">
                <i class="material-icons prefix" aria-hidden="true">search</i>
                <input
                    id="search-results-q"
                    type="search"
                    v-model="searchTerm"
                    v-on:keyup.enter="performSearch"
                    placeholder="{{ lang('search_palette_placeholder') }}"
                    aria-label="{{ lang('search') }}"
                    autocomplete="off"
                >
            </div>
        </div>
        <div class="search-palette__chips" v-if="chipTypes.length > 1">
            <button
                type="button"
                class="status-chip"
                v-for="chip in chipTypes"
                :key="chip.id"
                :class="{ active: typeFilter === chip.id }"
                @click="setTypeFilter(chip.id)"
            >
                @{{ chip.label }}
                <span class="search-palette__chip-count" v-if="chip.id !== 'all'">@{{ chip.count }}</span>
            </button>
        </div>
        <div class="search-empty" v-if="showIdle">
            <i class="material-icons" aria-hidden="true">search</i>
            <p>{{ lang('search_no_query') }}</p>
        </div>
        <div class="search-empty" v-else-if="showMinChars">
            <i class="material-icons" aria-hidden="true">search</i>
            <p>{{ lang('search_min_chars') }}</p>
        </div>
        <div class="search-empty" v-else-if="showNoResults">
            <i class="material-icons" aria-hidden="true">search_off</i>
            <p>@{{ noResultsLabel }}</p>
            <button type="button" class="btn-flat" @click="clearSearch">{{ lang('search_empty_cta') }}</button>
        </div>
        <ul class="search-hit-list" v-else>
            <li v-for="hit in visibleHits" :key="hit.id">
                <search-hit :hit="hit"></search-hit>
            </li>
        </ul>
    </div>
</div>
@endsection

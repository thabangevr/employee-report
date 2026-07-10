@push('styles')
<style>
    .form-section { margin-bottom: 28px; }
    .form-label-ev {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ev-text-heading);
        margin-bottom: 8px;
    }
    .form-control-ev {
        border: 1px solid var(--ev-border);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: var(--ev-text);
        width: 100%;
        transition: border-color 0.15s;
    }
    .form-control-ev:focus {
        border-color: var(--ev-green);
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
    .repeater-item {
        background: var(--ev-bg);
        border: 1px solid var(--ev-border-light);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 10px;
        position: relative;
    }
    .repeater-remove {
        position: absolute;
        top: 8px;
        right: 10px;
        background: none;
        border: none;
        color: var(--ev-red);
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
        padding: 2px 6px;
    }
    .repeater-add {
        background: none;
        border: 1px dashed var(--ev-border);
        border-radius: 8px;
        padding: 10px;
        width: 100%;
        font-size: 13px;
        color: var(--ev-text-secondary);
        cursor: pointer;
        transition: border-color 0.15s, color 0.15s;
    }
    .repeater-add:hover {
        border-color: var(--ev-green);
        color: var(--ev-green);
    }
    .area-block {
        background: var(--ev-white);
        border: 1px solid var(--ev-border);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .word-counter {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .word-counter.ok { background: var(--ev-green-bg); color: var(--ev-green-value); }
    .word-counter.warn { background: var(--ev-amber-bg); color: var(--ev-amber-value); }
    .word-counter.over { background: var(--ev-red-bg); color: var(--ev-red); }
</style>
@endpush

<div x-data="weeklySubmissionForm()" x-init="init()">
    {{-- Word Count --}}
    <div class="d-flex justify-content-end mb-3">
        <span class="word-counter" :class="wordCountClass" x-text="wordCount + ' / 200 words'"></span>
    </div>

    {{-- OKR Focus --}}
    @if($okrs->isNotEmpty())
        <div class="form-section">
            <div class="section-title">OKR Focus This Week</div>
            <div class="ev-card">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($okrs as $okr)
                        <label style="cursor:pointer;">
                            <input type="checkbox" name="okr_focus_ids[]" value="{{ $okr->id }}"
                                {{ in_array($okr->id, old('okr_focus_ids', $selectedOkrIds ?? [])) ? 'checked' : '' }}
                                style="display:none;" x-ref="okr_{{ $okr->id }}">
                            <span style="padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;border:1px solid var(--ev-border);display:inline-block;transition:all 0.15s;"
                                :style="$refs.okr_{{ $okr->id }}.checked ? 'background:var(--ev-green);color:#fff;border-color:var(--ev-green)' : ''"
                                @click="$nextTick(() => { $refs.okr_{{ $okr->id }}.checked = !$refs.okr_{{ $okr->id }}.checked; })">
                                {{ $okr->title }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Headline Number --}}
    <div class="form-section">
        <div class="section-title">Headline Number</div>
        <div class="ev-card">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-ev">Value</label>
                    <input type="text" name="one_number_value" class="form-control-ev"
                        value="{{ old('one_number_value', $submission->one_number_value ?? '') }}"
                        placeholder="e.g. 42%">
                </div>
                <div class="col-md-8">
                    <label class="form-label-ev">Label / Description</label>
                    <input type="text" name="one_number_label" class="form-control-ev"
                        value="{{ old('one_number_label', $submission->one_number_label ?? '') }}"
                        placeholder="e.g. Sprint completion rate">
                </div>
            </div>
        </div>
    </div>

    {{-- Areas --}}
    <div class="form-section">
        <div class="section-title">Areas</div>

        <template x-for="(area, areaIndex) in areas" :key="areaIndex">
            <div class="area-block">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <input type="text" :name="'areas[' + areaIndex + '][name]'" class="form-control-ev" style="max-width:400px;"
                        x-model="area.name" placeholder="Area name" required>
                    <button type="button" class="repeater-remove" @click="removeArea(areaIndex)" x-show="areas.length > 1">&times;</button>
                </div>

                <input type="hidden" :name="'areas[' + areaIndex + '][manager_area_id]'" :value="area.manager_area_id || ''">

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label-ev">Status</label>
                    <div class="d-flex gap-2 mb-2">
                        <template x-for="statusOption in ['green', 'amber', 'blocker']" :key="statusOption">
                            <label style="cursor:pointer;">
                                <input type="radio" :name="'areas[' + areaIndex + '][status]'" :value="statusOption"
                                    x-model="area.status" style="display:none;">
                                <span class="pill"
                                    :class="{
                                        'pill-green': statusOption === 'green',
                                        'pill-amber': statusOption === 'amber',
                                        'pill-blocker': statusOption === 'blocker'
                                    }"
                                    :style="area.status === statusOption ? 'outline:2px solid var(--ev-text);outline-offset:2px;' : 'opacity:0.5;'"
                                    x-text="statusOption.charAt(0).toUpperCase() + statusOption.slice(1)">
                                </span>
                            </label>
                        </template>
                    </div>
                    <textarea :name="'areas[' + areaIndex + '][status_justification]'" class="form-control-ev"
                        x-model="area.status_justification" rows="2" placeholder="Justification (required for amber/blocker)"
                        @input="updateWordCount()"></textarea>
                </div>

                {{-- Outcomes --}}
                <div class="mb-3">
                    <label class="form-label-ev">Last Week Outcomes (2-4)</label>
                    <template x-for="(outcome, outIdx) in area.outcomes" :key="outIdx">
                        <div class="repeater-item">
                            <button type="button" class="repeater-remove" @click="area.outcomes.splice(outIdx, 1); updateWordCount()" x-show="area.outcomes.length > 1">&times;</button>
                            <textarea :name="'areas[' + areaIndex + '][outcomes][' + outIdx + '][description]'"
                                class="form-control-ev mb-2" x-model="outcome.description" rows="2"
                                placeholder="What was achieved?" @input="updateWordCount()" required></textarea>
                            <select :name="'areas[' + areaIndex + '][outcomes][' + outIdx + '][okr_id]'" class="form-control-ev" x-model="outcome.okr_id">
                                <option value="">Tag OKR (optional)</option>
                                @foreach($okrs as $okr)
                                    <option value="{{ $okr->id }}">{{ $okr->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </template>
                    <button type="button" class="repeater-add" @click="area.outcomes.push({description:'', okr_id:''})" x-show="area.outcomes.length < 4">
                        + Add Outcome
                    </button>
                </div>

                {{-- Priorities --}}
                <div>
                    <label class="form-label-ev">This Week Priorities (2-4)</label>
                    <template x-for="(priority, priIdx) in area.priorities" :key="priIdx">
                        <div class="repeater-item">
                            <button type="button" class="repeater-remove" @click="area.priorities.splice(priIdx, 1); updateWordCount()" x-show="area.priorities.length > 1">&times;</button>
                            <textarea :name="'areas[' + areaIndex + '][priorities][' + priIdx + '][description]'"
                                class="form-control-ev mb-2" x-model="priority.description" rows="2"
                                placeholder="What will you focus on?" @input="updateWordCount()" required></textarea>
                            <select :name="'areas[' + areaIndex + '][priorities][' + priIdx + '][okr_id]'" class="form-control-ev" x-model="priority.okr_id">
                                <option value="">Tag OKR (optional)</option>
                                @foreach($okrs as $okr)
                                    <option value="{{ $okr->id }}">{{ $okr->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </template>
                    <button type="button" class="repeater-add" @click="area.priorities.push({description:'', okr_id:''})" x-show="area.priorities.length < 4">
                        + Add Priority
                    </button>
                </div>
            </div>
        </template>

        <button type="button" class="repeater-add" @click="addArea()">
            + Add Area
        </button>
    </div>

    {{-- Flags --}}
    <div class="form-section">
        <div class="section-title">Flags</div>
        <div class="ev-card">
            <template x-for="(flag, flagIdx) in flags" :key="flagIdx">
                <div class="repeater-item">
                    <button type="button" class="repeater-remove" @click="flags.splice(flagIdx, 1); updateWordCount()">&times;</button>
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label-ev">Risk</label>
                            <input type="text" :name="'flags[' + flagIdx + '][risk]'" class="form-control-ev"
                                x-model="flag.risk" placeholder="What is at risk?" @input="updateWordCount()" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-ev">Cause</label>
                            <input type="text" :name="'flags[' + flagIdx + '][cause]'" class="form-control-ev"
                                x-model="flag.cause" placeholder="Why?" @input="updateWordCount()" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-ev">Consequence</label>
                            <input type="text" :name="'flags[' + flagIdx + '][consequence]'" class="form-control-ev"
                                x-model="flag.consequence" placeholder="What happens if not addressed?" @input="updateWordCount()" required>
                        </div>
                    </div>
                </div>
            </template>
            <button type="button" class="repeater-add" @click="flags.push({risk:'', cause:'', consequence:''})">
                + Add Flag
            </button>
        </div>
    </div>

    {{-- Cross-team Actions --}}
    <div class="form-section">
        <div class="section-title">Cross-team Actions</div>
        <div class="ev-card">
            <template x-for="(cta, ctaIdx) in crossTeamActions" :key="ctaIdx">
                <div class="repeater-item">
                    <button type="button" class="repeater-remove" @click="crossTeamActions.splice(ctaIdx, 1); updateWordCount()">&times;</button>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label-ev">Owner</label>
                            <input type="text" :name="'cross_team_actions[' + ctaIdx + '][owner_name]'" class="form-control-ev"
                                x-model="cta.owner_name" placeholder="Person/Team name" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label-ev">Ask</label>
                            <input type="text" :name="'cross_team_actions[' + ctaIdx + '][ask]'" class="form-control-ev"
                                x-model="cta.ask" placeholder="What do you need from them?" @input="updateWordCount()" required>
                        </div>
                    </div>
                </div>
            </template>
            <button type="button" class="repeater-add" @click="crossTeamActions.push({owner_name:'', ask:''})">
                + Add Cross-team Action
            </button>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="ev-card mb-4" style="border-color:var(--ev-red);background:var(--ev-red-bg);">
            <ul class="mb-0" style="color:var(--ev-red);font-size:13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Submit --}}
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('weekly-submissions.index') }}" class="btn-ev-outline">Cancel</a>
        <div class="d-flex gap-2">
            <button type="submit" name="action" value="draft" class="btn-ev-outline">Save Draft</button>
            <button type="submit" name="action" value="submit" class="btn-ev-primary">Save & Submit</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function weeklySubmissionForm() {
    return {
        areas: @json($formAreas),
        flags: @json($formFlags),
        crossTeamActions: @json($formCrossTeamActions),
        wordCount: 0,

        get wordCountClass() {
            if (this.wordCount > 200) return 'over';
            if (this.wordCount >= 150) return 'ok';
            return 'warn';
        },

        init() {
            this.updateWordCount();
        },

        addArea() {
            this.areas.push({
                name: '',
                manager_area_id: null,
                status: null,
                status_justification: '',
                outcomes: [{description: '', okr_id: ''}],
                priorities: [{description: '', okr_id: ''}]
            });
        },

        removeArea(index) {
            this.areas.splice(index, 1);
            this.updateWordCount();
        },

        updateWordCount() {
            let text = '';
            this.areas.forEach(area => {
                text += ' ' + (area.status_justification || '');
                (area.outcomes || []).forEach(o => { text += ' ' + (o.description || ''); });
                (area.priorities || []).forEach(p => { text += ' ' + (p.description || ''); });
            });
            this.flags.forEach(f => {
                text += ' ' + (f.risk || '') + ' ' + (f.cause || '') + ' ' + (f.consequence || '');
            });
            this.crossTeamActions.forEach(c => {
                text += ' ' + (c.ask || '');
            });
            this.wordCount = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        }
    };
}
</script>
@endpush

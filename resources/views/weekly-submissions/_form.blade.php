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

    /* Mode toggle */
    .mode-toggle {
        display: flex;
        background: var(--ev-bg);
        border: 1px solid var(--ev-border);
        border-radius: 10px;
        padding: 4px;
        margin-bottom: 24px;
    }
    .mode-toggle-btn {
        flex: 1;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
        color: var(--ev-text-secondary);
        text-align: center;
    }
    .mode-toggle-btn.active {
        background: var(--ev-white);
        color: var(--ev-text);
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .mode-toggle-btn:hover:not(.active) {
        color: var(--ev-text);
    }
    .mode-toggle-icon {
        display: inline-block;
        margin-right: 6px;
        font-size: 14px;
    }

    /* AI paste area */
    .ai-paste-area {
        border: 2px dashed var(--ev-border);
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 24px;
        transition: border-color 0.2s;
    }
    .ai-paste-area:focus-within {
        border-color: var(--ev-green);
    }
    .ai-paste-textarea {
        border: 1px solid var(--ev-border);
        border-radius: 8px;
        padding: 14px;
        font-size: 14px;
        color: var(--ev-text);
        width: 100%;
        min-height: 200px;
        resize: vertical;
        font-family: inherit;
        line-height: 1.55;
    }
    .ai-paste-textarea:focus {
        border-color: var(--ev-green);
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
    .ai-paste-textarea::placeholder {
        color: var(--ev-text-secondary);
    }
    .btn-ai-extract {
        background: var(--ev-green);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-ai-extract:hover { opacity: 0.9; }
    .btn-ai-extract:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* AI status messages */
    .ai-status {
        font-size: 13px;
        padding: 10px 14px;
        border-radius: 8px;
        margin-top: 12px;
    }
    .ai-status-loading {
        background: var(--ev-tag-bg);
        color: var(--ev-tag-text);
    }
    .ai-status-success {
        background: var(--ev-green-bg);
        color: var(--ev-green-value);
    }
    .ai-status-error {
        background: var(--ev-red-bg);
        color: var(--ev-red);
    }

    /* Spinner */
    .spinner-sm {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
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
                        <label style="cursor:pointer;" @click.prevent="toggleOkr({{ $okr->id }})">
                            <input type="checkbox" name="okr_focus_ids[]" value="{{ $okr->id }}"
                                :checked="selectedOkrIds.includes({{ $okr->id }})"
                                style="display:none;">
                            <span style="padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;border:1px solid var(--ev-border);display:inline-block;transition:all 0.15s;"
                                :style="selectedOkrIds.includes({{ $okr->id }}) ? 'background:var(--ev-green);color:#fff;border-color:var(--ev-green)' : ''">
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
                        x-model="oneNumberValue"
                        placeholder="e.g. 42%">
                </div>
                <div class="col-md-8">
                    <label class="form-label-ev">Label / Description</label>
                    <input type="text" name="one_number_label" class="form-control-ev"
                        x-model="oneNumberLabel"
                        placeholder="e.g. Sprint completion rate">
                </div>
            </div>
        </div>
    </div>

    {{-- Mode Toggle + Reuse --}}
    <div class="form-section">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div class="section-title" style="margin-bottom:0;">Areas, Outcomes & Flags</div>
            @if(!isset($submission))
                <button type="button" class="btn-ev-outline" style="font-size:12px;padding:6px 14px;"
                    @click="reuseLastWeek()" :disabled="reusingLastWeek">
                    <span x-show="reusingLastWeek" style="display:inline-block;width:12px;height:12px;border:2px solid var(--ev-text-secondary);border-top-color:var(--ev-green);border-radius:50%;animation:spin 0.6s linear infinite;margin-right:4px;vertical-align:middle;"></span>
                    <span x-text="reusingLastWeek ? 'Loading...' : '&#8634; Reuse last week'"></span>
                </button>
            @endif
        </div>
        <div class="mode-toggle">
            <button type="button" class="mode-toggle-btn" :class="{ active: mode === 'ai' }" @click="mode = 'ai'">
                <span class="mode-toggle-icon">&#9889;</span> Paste & Analyze
            </button>
            <button type="button" class="mode-toggle-btn" :class="{ active: mode === 'manual' }" @click="mode = 'manual'">
                <span class="mode-toggle-icon">&#9998;</span> Enter Manually
            </button>
        </div>
    </div>

    {{-- AI Paste Mode --}}
    <div x-show="mode === 'ai'" x-cloak>
        <div class="form-section">
            <div class="ai-paste-area">
                <label class="form-label-ev" style="margin-bottom:12px;">Paste your weekly update</label>
                <textarea class="ai-paste-textarea" x-model="rawContent"
                    placeholder="Paste your raw weekly update here. Include areas you worked on, what you achieved last week, your priorities for this week, any risks or blockers...

Example:
Platform team - things are going well. Last week we shipped the new auth flow and fixed 3 critical bugs. This week focusing on API rate limiting and monitoring setup.

Growth team - amber status, behind on onboarding redesign due to design resource constraints. Completed A/B test analysis showing 12% improvement. This week: finalize new onboarding flow mockups.

Risk: API migration deadline is tight. Cause: dependency on external vendor API changes. Could delay Q3 launch if not resolved by end of month."></textarea>

                <div class="d-flex align-items-center justify-content-between mt-3">
                    <button type="button" class="btn-ai-extract" @click="analyzeContent()" :disabled="analyzing || !rawContent.trim()">
                        <template x-if="analyzing">
                            <span class="spinner-sm"></span>
                        </template>
                        <template x-if="!analyzing">
                            <span>&#9889;</span>
                        </template>
                        <span x-text="analyzing ? 'Analyzing...' : 'Extract with AI'"></span>
                    </button>

                    <span x-show="aiExtracted" style="font-size:13px;color:var(--ev-green-value);font-weight:600;">
                        &#10003; Data extracted — review below or switch to manual to edit
                    </span>
                </div>

                <div x-show="aiStatus" class="ai-status" :class="aiStatusClass" x-text="aiStatus"></div>
            </div>
        </div>
    </div>

    {{-- Areas (shown in both modes, editable always) --}}
    <div class="form-section" x-show="mode === 'manual' || aiExtracted">
        <template x-if="mode === 'ai' && aiExtracted">
            <div style="background:var(--ev-highlight-bg);border:1px solid var(--ev-highlight-border);border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--ev-text-body);">
                AI-extracted data shown below. You can edit any field directly, or switch to <strong>Enter Manually</strong> to use the full editor.
            </div>
        </template>

        <template x-if="mode === 'manual'">
            <div class="section-title">Areas</div>
        </template>

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
    <div class="form-section" x-show="mode === 'manual' || aiExtracted">
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
        mode: 'manual',
        rawContent: '',
        analyzing: false,
        aiExtracted: false,
        aiStatus: '',
        aiStatusClass: '',
        reusingLastWeek: false,
        selectedOkrIds: @json(array_map('intval', old('okr_focus_ids', $selectedOkrIds ?? []))),
        oneNumberValue: @json(old('one_number_value', $submission->one_number_value ?? '')),
        oneNumberLabel: @json(old('one_number_label', $submission->one_number_label ?? '')),
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

        toggleOkr(id) {
            const idx = this.selectedOkrIds.indexOf(id);
            if (idx === -1) {
                this.selectedOkrIds.push(id);
            } else {
                this.selectedOkrIds.splice(idx, 1);
            }
        },

        async reuseLastWeek() {
            if (this.reusingLastWeek) return;
            this.reusingLastWeek = true;

            try {
                const response = await fetch('{{ route("weekly-submissions.previous-data") }}', {
                    headers: { 'Accept': 'application/json' },
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.error || 'No previous submission found.');
                    return;
                }

                this.areas = data.areas;
                this.flags = data.flags || [];
                this.crossTeamActions = data.cross_team_actions || [];
                this.oneNumberValue = data.one_number_value || '';
                this.oneNumberLabel = data.one_number_label || '';
                this.selectedOkrIds = (data.okr_focus_ids || []).map(id => parseInt(id));

                this.mode = 'manual';
                this.updateWordCount();
            } catch (error) {
                alert('Failed to load previous submission. Please try again.');
            } finally {
                this.reusingLastWeek = false;
            }
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

        async analyzeContent() {
            if (!this.rawContent.trim() || this.analyzing) return;

            this.analyzing = true;
            this.aiStatus = 'Analyzing your update with AI...';
            this.aiStatusClass = 'ai-status-loading';

            try {
                const response = await fetch('{{ route("weekly-submissions.analyze") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ raw_content: this.rawContent }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || data.message || 'Analysis failed');
                }

                if (data.areas && data.areas.length > 0) {
                    this.areas = data.areas;
                }
                if (data.flags) {
                    this.flags = data.flags;
                }

                this.aiExtracted = true;
                this.aiStatus = '';
                this.aiStatusClass = '';
                this.updateWordCount();
            } catch (error) {
                this.aiStatus = error.message || 'Something went wrong. Please try again or enter manually.';
                this.aiStatusClass = 'ai-status-error';
            } finally {
                this.analyzing = false;
            }
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

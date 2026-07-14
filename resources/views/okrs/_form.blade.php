@push('styles')
<style>
    .okr-form-section {
        margin-bottom: 24px;
    }
    .okr-form-section:last-child {
        margin-bottom: 0;
    }
    .form-label-okr {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ev-text-heading);
        margin-bottom: 6px;
        display: block;
    }
    .form-input-okr {
        border: 1px solid var(--ev-border);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: var(--ev-text);
        width: 100%;
        transition: border-color 0.15s;
        background: var(--ev-white);
    }
    .form-input-okr:focus {
        border-color: var(--ev-green);
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
    .form-textarea-okr {
        border: 1px solid var(--ev-border);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: var(--ev-text);
        width: 100%;
        resize: vertical;
        min-height: 70px;
        transition: border-color 0.15s;
        background: var(--ev-white);
    }
    .form-textarea-okr:focus {
        border-color: var(--ev-green);
        outline: none;
        box-shadow: 0 0 0 3px rgba(108,191,62,0.12);
    }
    .weight-input-group {
        position: relative;
        max-width: 120px;
    }
    .weight-input-group input {
        padding-right: 32px;
    }
    .weight-input-group .percent-sign {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ev-text-secondary);
        font-size: 14px;
        font-weight: 600;
        pointer-events: none;
    }
    .kr-section {
        background: var(--ev-bg);
        border: 1px solid var(--ev-border-light);
        border-radius: 10px;
        padding: 16px;
    }
    .kr-section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--ev-text-heading);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .kr-section-title .kr-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .kr-badge-lag {
        background: var(--ev-tag-bg);
        color: var(--ev-tag-text);
    }
    .kr-badge-lead {
        background: var(--ev-green-bg);
        color: var(--ev-green-value);
    }
    .kr-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .kr-item:last-child {
        margin-bottom: 0;
    }
    .kr-item input {
        flex: 1;
    }
    .kr-remove-btn {
        background: none;
        border: 1px solid var(--ev-border);
        border-radius: 6px;
        width: 32px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--ev-text-secondary);
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .kr-remove-btn:hover {
        border-color: var(--ev-red);
        color: var(--ev-red);
        background: var(--ev-red-bg);
    }
    .kr-add-btn {
        background: none;
        border: 1px dashed var(--ev-border);
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ev-text-secondary);
        cursor: pointer;
        transition: all 0.15s;
        width: 100%;
        text-align: left;
        margin-top: 8px;
    }
    .kr-add-btn:hover {
        border-color: var(--ev-green);
        color: var(--ev-green);
        background: var(--ev-highlight-bg);
    }
    .form-hint {
        font-size: 12px;
        color: var(--ev-text-secondary);
        margin-top: 4px;
    }
    .form-error {
        color: var(--ev-red);
        font-size: 12px;
        margin-top: 4px;
    }
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 20px;
        border-top: 1px solid var(--ev-border-light);
    }
</style>
@endpush

@php
    $isEdit = isset($okr);
    $formTitle = $isEdit ? $okr->title : old('title', '');
    $formDescription = $isEdit ? $okr->objective_description : old('objective_description', '');
    $formMeasure = $isEdit ? $okr->measure_of_success : old('measure_of_success', '');
    $formWeight = $isEdit ? $okr->weight : old('weight', '');
    $formIsActive = $isEdit ? $okr->is_active : true;

    $existingLag = [];
    $existingLead = [];

    if ($isEdit) {
        foreach ($okr->keyResults as $kr) {
            if ($kr->type === 'lag_measure') {
                $existingLag[] = ['id' => $kr->id, 'description' => $kr->description];
            } else {
                $existingLead[] = ['id' => $kr->id, 'description' => $kr->description];
            }
        }
    }

    if (old('key_results')) {
        $existingLag = [];
        $existingLead = [];
        foreach (old('key_results') as $kr) {
            $item = ['id' => $kr['id'] ?? null, 'description' => $kr['description'] ?? ''];
            if (($kr['type'] ?? '') === 'lag_measure') {
                $existingLag[] = $item;
            } else {
                $existingLead[] = $item;
            }
        }
    }
@endphp

<div x-data="okrForm()" x-init="init()">
    {{-- Objective --}}
    <div class="okr-form-section">
        <div class="section-title">Objective</div>
        <div class="ev-card">
            <div class="mb-3">
                <label class="form-label-okr" for="okr-title">Objective Title <span style="color:var(--ev-red)">*</span></label>
                <input type="text" id="okr-title" name="title" class="form-input-okr"
                    value="{{ $formTitle }}"
                    placeholder="e.g. Increase monthly revenue by 30%" required>
                <div class="form-hint">A clear, aspirational goal that defines what you want to achieve.</div>
                @error('title')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="form-label-okr" for="okr-description">Description</label>
                <textarea id="okr-description" name="objective_description" class="form-textarea-okr"
                    placeholder="Optional — provide more context about this objective">{{ $formDescription }}</textarea>
                @error('objective_description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Weight & Measure of Success --}}
    <div class="okr-form-section">
        <div class="section-title">Weight & Success Criteria</div>
        <div class="ev-card">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label-okr" for="okr-weight">Weight <span style="color:var(--ev-red)">*</span></label>
                    <div class="weight-input-group">
                        <input type="number" id="okr-weight" name="weight" class="form-input-okr"
                            value="{{ $formWeight }}"
                            min="0" max="100" step="1" placeholder="25" required>
                        <span class="percent-sign">%</span>
                    </div>
                    <div class="form-hint">All active OKRs must total 100%.</div>
                    @error('weight')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-9">
                    <label class="form-label-okr" for="okr-measure">Measure of Success</label>
                    <input type="text" id="okr-measure" name="measure_of_success" class="form-input-okr"
                        value="{{ $formMeasure }}"
                        placeholder="e.g. Close 5 deals, grow database list to 10k subscribers">
                    <div class="form-hint">What does success look like? Be specific and measurable.</div>
                    @error('measure_of_success')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Key Results --}}
    <div class="okr-form-section">
        <div class="section-title">Key Results</div>
        <div class="ev-card" style="padding:0;overflow:hidden;">
            <div class="row g-0">
                {{-- Lag Measures --}}
                <div class="col-md-6" style="padding:16px;border-right:1px solid var(--ev-border-light);">
                    <div class="kr-section">
                        <div class="kr-section-title">
                            <span class="kr-badge kr-badge-lag">Lag</span>
                            Outcomes
                        </div>
                        <div class="form-hint" style="margin-top:-4px;margin-bottom:12px;">
                            Measurable results — the outcomes you're tracking.
                        </div>

                        <template x-for="(measure, index) in lagMeasures" :key="'lag-' + index">
                            <div class="kr-item">
                                <input type="hidden" :name="'key_results[' + getLagIndex(index) + '][type]'" value="lag_measure">
                                <template x-if="measure.id">
                                    <input type="hidden" :name="'key_results[' + getLagIndex(index) + '][id]'" :value="measure.id">
                                </template>
                                <input type="text" class="form-input-okr"
                                    :name="'key_results[' + getLagIndex(index) + '][description]'"
                                    x-model="measure.description"
                                    placeholder="e.g. Revenue reaches R500k/month">
                                <button type="button" class="kr-remove-btn" @click="removeLag(index)" title="Remove">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 3l8 8M11 3l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </template>

                        <button type="button" class="kr-add-btn" @click="addLag()">+ Add lag measure</button>
                    </div>
                </div>

                {{-- Lead Measures --}}
                <div class="col-md-6" style="padding:16px;">
                    <div class="kr-section">
                        <div class="kr-section-title">
                            <span class="kr-badge kr-badge-lead">Lead</span>
                            Predictive Actions
                        </div>
                        <div class="form-hint" style="margin-top:-4px;margin-bottom:12px;">
                            Activities you control — actions that drive the outcomes.
                        </div>

                        <template x-for="(measure, index) in leadMeasures" :key="'lead-' + index">
                            <div class="kr-item">
                                <input type="hidden" :name="'key_results[' + getLeadIndex(index) + '][type]'" value="lead_measure">
                                <template x-if="measure.id">
                                    <input type="hidden" :name="'key_results[' + getLeadIndex(index) + '][id]'" :value="measure.id">
                                </template>
                                <input type="text" class="form-input-okr"
                                    :name="'key_results[' + getLeadIndex(index) + '][description]'"
                                    x-model="measure.description"
                                    placeholder="e.g. Make 50 cold calls per week">
                                <button type="button" class="kr-remove-btn" @click="removeLead(index)" title="Remove">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 3l8 8M11 3l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </template>

                        <button type="button" class="kr-add-btn" @click="addLead()">+ Add lead measure</button>
                    </div>
                </div>
            </div>
        </div>
        @error('key_results')
            <div class="form-error">{{ $message }}</div>
        @enderror
        @error('key_results.*')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    @if($isEdit)
        <input type="hidden" name="is_active" value="{{ $formIsActive ? '1' : '0' }}">
    @endif

    {{-- Actions --}}
    <div class="form-actions">
        <a href="{{ route('okrs.index') }}" class="btn-ev-outline">Cancel</a>
        <button type="submit" class="btn-ev-primary">
            {{ $isEdit ? 'Update OKR' : 'Create OKR' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
    function okrForm() {
        return {
            lagMeasures: [],
            leadMeasures: [],

            init() {
                const existingLag = @json($existingLag);
                const existingLead = @json($existingLead);

                this.lagMeasures = existingLag.length > 0
                    ? existingLag
                    : [{ id: null, description: '' }];

                this.leadMeasures = existingLead.length > 0
                    ? existingLead
                    : [{ id: null, description: '' }];
            },

            addLag() {
                this.lagMeasures.push({ id: null, description: '' });
            },

            removeLag(index) {
                if (this.lagMeasures.length > 0) {
                    this.lagMeasures.splice(index, 1);
                }
            },

            addLead() {
                this.leadMeasures.push({ id: null, description: '' });
            },

            removeLead(index) {
                if (this.leadMeasures.length > 0) {
                    this.leadMeasures.splice(index, 1);
                }
            },

            getLagIndex(index) {
                return index;
            },

            getLeadIndex(index) {
                return this.lagMeasures.length + index;
            },
        }
    }
</script>
@endpush

<!-- Submit Progress Modal -->
<div class="modal fade" id="submitProgressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white">
                    <i class="bx bx-upload"></i> Submit Progress Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="submitProgressFormModal" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="action" value="task_submit_report">
                    
                    <div class="mb-3">
                        <label class="form-label">Activity *</label>
                        <select name="activity_id" id="modalProgressActivitySelect" class="form-select" required>
                            <option value="">Select activity</option>
                            @foreach($flatActivities as $activity)
                                <option value="{{ $activity['id'] }}">
                                    {{ $activity['name'] }} — {{ $activity['task'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Report Date</label>
                            <input type="date" name="report_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Completion Status *</label>
                            <select name="completion_status" id="modalCompletionStatus" class="form-select" required>
                                <option value="On Track">On Track</option>
                                <option value="Ahead">Ahead</option>
                                <option value="Behind Schedule">Behind Schedule</option>
                                <option value="Delayed">Delayed</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" id="modalReportAttachments" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.xls,.xlsx,.txt">
                        <small class="text-muted">Upload documents, images, or other files (multiple files allowed)</small>
                        <div id="modalAttachmentPreview" class="mt-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Work Done *</label>
                        <textarea name="work_description" rows="4" class="form-control" placeholder="Be concise but detailed. What did you complete?" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Next Activities</label>
                        <textarea name="next_activities" rows="2" class="form-control" placeholder="What's next? Who needs to act?"></textarea>
                    </div>
                    
                    <div class="mb-3" id="modalDelayReasonWrap" style="display:none;">
                        <label class="form-label">Reason / Blockers</label>
                        <textarea name="reason_if_delayed" rows="2" class="form-control" placeholder="Why is it behind? What support is needed?"></textarea>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle"></i> SMS alerts will be sent to leaders and approvers automatically.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#modalCompletionStatus').on('change', function() {
        const status = $(this).val();
        if (status === 'Behind Schedule' || status === 'Delayed') {
            $('#modalDelayReasonWrap').show();
        } else {
            $('#modalDelayReasonWrap').hide();
        }
    });
    
    // Preview selected files
    $('#modalReportAttachments').on('change', function() {
        const files = this.files;
        const preview = $('#modalAttachmentPreview');
        preview.empty();
        
        if (files.length > 0) {
            preview.append('<small class="text-muted">Selected files:</small><ul class="list-unstyled mt-1">');
            Array.from(files).forEach(function(file) {
                const size = (file.size / 1024).toFixed(2);
                preview.append(`<li><i class="bx bx-file"></i> ${file.name} <small class="text-muted">(${size} KB)</small></li>`);
            });
            preview.append('</ul>');
        }
    });
});
</script>


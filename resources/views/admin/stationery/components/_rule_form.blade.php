<!-- Allocation Rule Form Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="ruleModalTitle">Allocation Rule</h3>
            </div>
            <form id="ruleForm" method="POST">
                @csrf
                <input type="hidden" id="rule_id" name="rule_id" value="">
                
                <div class="modal-body">
                    <!-- Stock Item Selection -->
                    <div class="form-group">
                        <label for="stock_item_id">Stock Item<span class="text-danger">*</span></label>
                        <select class="form-control" id="stock_item_id" name="stock_item_id">
                            <option value="">Select Stock Item</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Rule Type -->
                    <div class="form-group">
                        <label for="rule_type">Rule Type<span class="text-danger">*</span></label>
                        <select class="form-control" id="rule_type" name="rule_type">
                            <option value="per_candidate">Per Candidate</option>
                            <option value="per_center">Per Center</option>
                            <option value="fixed">Fixed Quantity</option>
                            <option value="conditional">Conditional</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Per Candidate:</strong> Quantity based on number of candidates<br>
                            <strong>Per Center:</strong> Quantity based on number of centers<br>
                            <strong>Fixed:</strong> Same quantity regardless of candidates/centers<br>
                            <strong>Conditional:</strong> Apply rule only if condition is met
                        </small>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Conditional Field (Hidden by default) -->
                    <div class="form-group conditional-field" style="display: none;">
                        <label for="condition_value">Condition: Minimum Candidates</label>
                        <input type="number" class="form-control" id="condition_value" name="condition_value" 
                               placeholder="e.g., 50 (apply rule only if candidates >= 50)">
                        <small class="form-text text-muted">Rule will only apply if number of candidates meets or exceeds this value</small>
                        <div class="invalid-feedback"></div>
                    </div>

                    <hr>
                    <h4>Base Calculation</h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="base_quantity">Base Quantity<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="base_quantity" 
                                       name="base_quantity" placeholder="e.g., 1" required>
                                <small class="form-text text-muted">Starting quantity per unit (candidate/center)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="multiplier">Multiplier (Safety Factor)<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="multiplier" 
                                       name="multiplier" placeholder="e.g., 1.05" required>
                                <small class="form-text text-muted">1.0 = no buffer, 1.1 = 10% buffer</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="extras-section">
                        <h4>Extras (Optional)</h4>
                        <p class="text-muted">Add additional quantities on top of the base calculation</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extras_fixed">Fixed Extras</label>
                                    <input type="number" step="0.01" class="form-control" id="extras_fixed" 
                                           name="extras_fixed" placeholder="e.g., 2">
                                    <small class="form-text text-muted">Add a fixed number (e.g., +2 sheets)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extras_per_candidate">Per Candidate Extras</label>
                                    <input type="number" step="0.01" class="form-control" id="extras_per_candidate" 
                                           name="extras_per_candidate" placeholder="e.g., 0.1">
                                    <small class="form-text text-muted">Add per candidate (e.g., +0.1 per candidate)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extras_percent">Percentage Extras</label>
                                    <input type="number" step="0.01" class="form-control" id="extras_percent" 
                                           name="extras_percent" placeholder="e.g., 10" max="100">
                                    <small class="form-text text-muted">Add % of base calculation (e.g., +10%)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extras_percent_candidates">Candidate % Extras</label>
                                    <input type="number" step="0.01" class="form-control" id="extras_percent_candidates" 
                                           name="extras_percent_candidates" placeholder="e.g., 5" max="100">
                                    <small class="form-text text-muted">Add % based on candidates (e.g., +5%)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle"></i> <strong>Calculation Formula:</strong><br>
                        <code>Result = (Base Qty × Count × Multiplier) + Fixed Extras + (Candidates × Per Candidate) + (Base × Percent%) + (Base × Candidate%)</code>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Allocation Rule</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
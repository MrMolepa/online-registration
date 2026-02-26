<!-- STEP 1: Personal Info & Service Selection -->
<section id="progress-form__panel-1" role="eservice-tabpanel" style="padding: 2rem;" aria-labelledby="progress-form__tab-1">
    <h4 class="mb-4 text-primary"><i class="fas fa-user-circle me-2"></i>Select Service & Personal Details</h4>

    <div class="form__field">
        <label for="select-service">
            <i class="fas fa-concierge-bell me-2"></i>Select Service Type
            <span data-required="true" aria-hidden="true"></span>
        </label>
        <select id="select-service" name="service" class="form-select" required>
            <option value="" disabled selected>-- Choose a service --</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}">{{ $service->description }}</option>
            @endforeach
            <option value="status">Check Application Status</option>
        </select>
        <small class="text-muted">Choose the service you require from the list above</small>
    </div>

    <div id="service-items-container" class="mt-4"></div>
    <div id="personal-info" class="mt-4"></div>

    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn-primary" id="next-to-step-2" disabled>
            Continue<i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</section>

<!-- STEP 2: Service Requirements -->
<section id="progress-form__panel-2" role="eservice-tabpanel" style="padding: 2rem;" aria-labelledby="progress-form__tab-2" hidden>
    <h4 class="mb-4 text-primary"><i class="fas fa-list-check me-2"></i>Service Requirements</h4>

    <div id="requirements-container" class="mt-4">
        <!-- Dynamically loaded requirements will be inserted here -->
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn-outline-secondary" id="back-to-step-1">
            <i class="fas fa-arrow-left me-2"></i>Back
        </button>
        <button type="button" class="btn-primary" id="next-to-step-3" disabled>
            Continue<i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</section>

<!-- STEP 3: Payment -->
<section id="progress-form__panel-3" role="eservice-tabpanel" style="padding: 2rem;" aria-labelledby="progress-form__tab-3" hidden>
    <h4 class="mb-4 text-primary"><i class="fas fa-credit-card me-2"></i>Payment Method</h4>

    <div class="payment-method">
        <!-- Payment options will be loaded here -->
    </div>

    <div id="payment-details" class="mt-4">
        <!-- Payment-specific details will be loaded here -->
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn-outline-secondary" id="back-to-step-2">
            <i class="fas fa-arrow-left me-2"></i>Back
        </button>
        <button type="submit" class="btn-primary" id="btn-submit" disabled>
            <i class="fas fa-check me-2"></i>Submit Payment
        </button>
    </div>
</section>

<!-- Thank You Message -->
<section id="progress-form__thank-you" role="eservice-tabpanel" style="padding: 2rem; display: none;" hidden>
    <div class="text-center">
        <div class="success-icon mb-4">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4 class="text-success mb-3">Payment Successful!</h4>
        <p class="mb-3">Thank you for your payment. Your application has been submitted successfully.</p>
        <p class="mb-4" id="thank-you-ref"></p>
        <button type="button" class="btn-primary" id="new-application">
            <i class="fas fa-plus me-2"></i>Submit Another Application
        </button>
    </div>
</section>

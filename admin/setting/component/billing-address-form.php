<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>First Name *</label>
            <input type="text" class="form-control" name="billing_address_first_name" value="<?=$row['billing_address_first_name']?>">
        </div>
    </div>
    <!--/span-->
    <div class="col-md-6">
        <div class="form-group">
            <label>Last Name *</label>
            <input type="text" class="form-control" name="billing_address_last_name" value="<?=$row['billing_address_last_name']?>">
        </div>
    </div>
    <!--/span-->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>Address *</label>
            <input type="text" class="form-control" name="billing_address_address" value="<?=$row['billing_address_address']?>">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>City *</label>
            <input type="text" class="form-control" name="billing_address_city" value="<?=$row['billing_address_city']?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Province *</label>
            <input type="text" class="form-control" name="billing_address_province" value="<?=$row['billing_address_province']?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Postal code *</label>
            <input type="text" class="form-control" name="billing_address_postal_code" value="<?=$row['billing_address_postal_code']?>">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Country *</label>
            <select name="billing_address_country" class="form-control" data-defvalue="<?=$row['billing_address_country']?>">
                <option value="">Select</option>
                <option value="Canada">Canada</option>
                <option value="United States">United States</option>
            </select>
        </div>
    </div>
    <div class="col-md-3"></div>
    <div class="col-md-3"></div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Phone Number *</label>
            <input type="text" class="form-control" name="billing_address_phone_number" value="<?=$row['billing_address_phone_number']?>">
        </div>
    </div>
    <div class="col-md-3">
        <label>Ext.</label>
        <input type="text" class="form-control" name="billing_address_phone_number_ext" value="<?=$row['billing_address_phone_number_ext']?>">
    </div>
    <div class="col-md-4"></div>
</div>
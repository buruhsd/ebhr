<template>
    <div class="card card-custom gutter-b">
        <div class="card-header">
            <h3 class="card-title">{{$route.meta.page}}</h3>
        </div>
        <div class="card-body">
            <form class="form" id="kt_form">
                <div class="col-xl-12">
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Avatar</label>
                        <div class="col-9">
                            <div class="image-input image-input-empty image-input-outline" id="kt_user_edit_avatar" style="background-image: url(/media/users/blank.png)">
                                <div class="image-input-wrapper"></div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg">
                                    <input type="hidden" name="profile_avatar_remove">
                                </label>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="" data-original-title="Remove avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">First Name</label>
                        <div class="col-9">
                            <input class="form-control form-control-lg form-control-solid" type="text" value="Anna">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Last Name</label>
                        <div class="col-9">
                            <input class="form-control form-control-lg form-control-solid" type="text" value="Krox">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Company Name</label>
                        <div class="col-9">
                            <input class="form-control form-control-lg form-control-solid" type="text" value="Loop Inc.">
                            <span class="form-text text-muted">If you want your invoices addressed to a company. Leave blank to use your full name.</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Contact Phone</label>
                        <div class="col-9">
                            <div class="input-group input-group-lg input-group-solid">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="la la-phone"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control form-control-lg form-control-solid" value="+45678967456" placeholder="Phone">
                            </div>
                            <span class="form-text text-muted">We'll never share your email with anyone else.</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Email Address</label>
                        <div class="col-9">
                            <div class="input-group input-group-lg input-group-solid">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="la la-at"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control form-control-lg form-control-solid" value="anna.krox@loop.com" placeholder="Email">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-3 text-lg-right text-left">Company Site</label>
                        <div class="col-9">
                            <div class="input-group input-group-lg input-group-solid">
                                <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Username" value="loop">
                                <div class="input-group-append">
                                    <span class="input-group-text">.com</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer text-right">
            <button type="reset" class="btn btn-primary mr-2">Submit</button>
            <button type="reset" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</template>
<script>
export default {
    created () {
        document.title = this.$route.meta.title;
    }
}
</script>

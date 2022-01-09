<div class="modal fade " id="variant-modal">
                <?= form_open('#', array('class' => '', 'id' => 'variant-form')); ?>
                <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                    <div class="modal-header header-custom">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <label aria-hidden="true">&times;</label></button>
                      <h4 class="modal-title text-center"><?= $this->lang->line('add_variant'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="box-body">
                              <div class="form-group">
                                <label for="variant"><?= $this->lang->line('variant'); ?>*</label>
                                <label id="variant_msg" class="text-danger text-right pull-right"></label>
                                <input type="text" class="form-control " id="variant" name="variant" placeholder="" >
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="box-body">
                              <div class="form-group">
                                <label for="description"><?= $this->lang->line('description'); ?></label>
                                <label id="description_msg" class="text-danger text-right pull-right"></label>
                                <textarea type="text" class="form-control" id="description" name="description" placeholder="" ></textarea>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <input type="hidden" name="store_id" value="<?=get_current_store_id();?>">
                      <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary add_variant">Save</button>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
               <?= form_close();?>
              </div>
              <!-- /.modal -->
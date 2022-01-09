<!-- **********************MODALS***************** -->
              <div class="modal fade" id="account-link-modal">
                <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                    <div class="modal-header header-custom">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title"><?= $this->lang->line('link_account'); ?></h4>
                    </div>
                    <div class="modal-body">
                      
                        <div class="row">
                          
                          <div class="col-md-12">
                            <div class="box-body">
                              <div class="form-group">
                                <label for="account_id"><?= $this->lang->line('account'); ?></label>
                                <select class="form-control select2" id='account_id' name="account_id" style="width: 100%;">
                                  <option value="">-Select-</option>}
                                  <?php
                                    echo get_accounts_select_list();
                                    ?>
                                </select>
                                <label id="account_id_msg" class="text-danger text-right pull-right"></label>
                              </div>
                            </div>
                          </div>
                        </div>
                     
                    </div>
                    <div class="modal-footer">
                      <input type="hidden" id="account_of" value="">
                      <input type="hidden" id="rec_id" value="">
                      <input type="hidden" id="prev_acc_id" value="">
                      <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary " onclick="update_account_link()">Update</button>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
              <!-- **********************MODALS END***************** -->
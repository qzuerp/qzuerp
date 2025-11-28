<div class="form" style="display:none;" id="8D">
    <!-- Genel Bilgiler -->
    <div class=" mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label required">Rapor No</label>
                <input type="text" class="form-control" name="report_no" value="{{ @$kart_veri->d8_report_no }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Tarih</label>
                <input type="date" class="form-control" name="report_date" value="{{ @$kart_veri->d8_report_date }}"
                    required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sorumlu Ekip</label>
                <input type="text" class="form-control" name="team" placeholder="Ekip adı / lider" value="{{ @$kart_veri->d8_team }}"">
            </div>
        </div>
    </div>

    <!-- Accordion D0-D8 -->
    <div class="accordion" id="accordion8d">

        <!-- D0: Hazırlık / Acil Önlem -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading0">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse0" aria-expanded="true">
                    D0 — Acil Önlem / Hazırlık
                </button>
            </h2>
            <div id="collapse0" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="mb-3">
                        <label class="form-label required">Kısa Tanım</label>
                        <input type="text" class="form-control" name="d0_short" value="{{ @$kart_veri->d8_d0_short }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alınan Hızlı Önlemler</label>
                        <textarea class="form-control textarea-resize" name="d0_containment"
                            placeholder="Hızlı/Geçici önlemler...">{{ @$kart_veri->d8_d0_containment }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- D1: Takım Kurma -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading1">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse1">
                    D1 — Takım
                </button>
            </h2>
            <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="mb-3">
                        <label class="form-label">Takım Üyeleri (Ad - Görev)</label>
                        <textarea class="form-control textarea-resize" name="d1_team"
                            placeholder="Örn: Ali Yılmaz - Süreç Lideri">{{ @$kart_veri->d8_d1_team }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- D2: Problem Tanımı -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse2">
                    D2 — Problem Tanımı
                </button>
            </h2>
            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="mb-3">
                        <label class="form-label required">Detaylı Açıklama</label>
                        <textarea class="form-control textarea-resize" name="d2_description" required>{{ @$kart_veri->d8_d2_description }}</textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Yer / Süreç</label>
                            <input type="text" class="form-control" name="d2_area" value="{{ @$kart_veri->d8_d2_area }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Frekans</label>
                            <input type="text" class="form-control" name="d2_frequency" value="{{ @$kart_veri->d8_d2_frequency }}" placeholder="Örn: 3/hafta">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ciddiyet / Priority</label>
                            <select class="form-select" name="d2_priority">
                                <option value="">Seç</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- D3: Geçici Tedbirler (Containment) -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse3">
                    D3 — Geçici Tedbirler / Containment
                </button>
            </h2>
            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div id="containmentList">
                        @if (@$kart_veri->d8_d3_containment)
                            @foreach (json_decode(@$kart_veri->d8_d3_containment) as $containment)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="d3_containment[]" value="{{ $containment }}">
                                    <button type="button" class="btn btn-danger remove-item">Sil</button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Yeni Geçici Tedbir Ekle</label>
                        <div class="input-group">
                            <input type="text" id="containmentInput" class="form-control"
                                placeholder="Tedbir açıklaması..." value="">
                            <button type="button" class="btn btn-primary" id="addContainment">Ekle</button>
                        </div>
                    </div>

                    <div class="mb-3 small-note">Mevcut tedbirleri listeden düzenleyebilir
                        veya silebilirsiniz.</div>
                </div>
            </div>
        </div>

        <!-- D4: Kök Neden Analizi -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse4">
                    D4 — Kök Neden Analizi
                </button>
            </h2>
            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="mb-3">
                        <label class="form-label">Kök Neden (5 Why / Ishikawa vb.)</label>
                        <textarea class="form-control textarea-resize" name="d4_rootcause">{{ @$kart_veri->d8_d4_rootcause }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kullanılan Metod</label>
                        <input type="text" class="form-control" name="d4_method" placeholder="Örn: 5-Why, Fishbone" value="{{ @$kart_veri->d8_d4_method }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- D5: Kalıcı Düzeltici Faaliyetler (Corrective Actions) -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading5">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse5">
                    D5 — Kalıcı Düzeltici Faaliyetler
                </button>
            </h2>
            <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div id="caList" class="mb-3">
                        @if (@$kart_veri->d8_d5_actions)
                            @foreach (json_decode(@$kart_veri->d8_d5_actions) as $ca)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="d5_action_desc[]" value="{{ $ca }}">
                                    <button type="button" class="btn btn-danger remove-item">Sil</button>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Yeni Faaliyet Ekle</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" id="caAction" class="form-control" placeholder="Faaliyet açıklaması">
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="caDue" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" id="addCA">Ekle</button>
                            </div>
                        </div>
                    </div>

                    <div class="small-note">Listeye ekledikten sonra düzenleyebilirsiniz.</div>
                </div>
            </div>
        </div>

        <!-- D6: Uygulama ve Doğrulama -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading6">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse6">
                    D6 — Uygulama & Doğrulama
                </button>
            </h2>
            <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="mb-3">
                        <label class="form-label">Uygulama Sonuçları / Testler</label>
                        <textarea class="form-control textarea-resize" name="d6_results">{{ @$kart_veri->d8_d6_results }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Doğrulama Tarihi</label>
                        <input type="date" class="form-control" name="d6_verified_at" value="{{ @$kart_veri->d8_d6_verified_at }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- D7: Önleyici Faaliyetler -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading7">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse7">
                    D7 — Önleyici Faaliyetler
                </button>
            </h2>
            <div id="collapse7" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="mb-3">
                        <label class="form-label">Önleyici Faaliyetler</label>
                        <textarea class="form-control textarea-resize" name="d7_preventive">{{ @$kart_veri->d8_d7_preventive }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- D8: Kapanış -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading8">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse8">
                    D8 — Kapanış / Onay
                </button>
            </h2>
            <div id="collapse8" class="accordion-collapse collapse" data-bs-parent="#accordion8d">
                <div class="accordion-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Kapanış Notu</label>
                            <textarea class="form-control textarea-resize" name="d8_closure">{{ @$kart_veri->d8_d8_closure }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Onaylayan</label>
                            <input type="text" class="form-control mb-2" value="{{ @$kart_veri->d8_d8_approved_by }}" name="d8_approved_by"
                                placeholder="İsim - Unvan" >
                            <label class="form-label">Onay Tarihi</label>
                            <input type="date" class="form-control" name="d8_approved_at" value="{{ @$kart_veri->d8_d8_approved_at }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /accordion -->

    <!-- Ekler ve Gönder -->
    <div class="card mt-3 mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Genel Notlar</label>
                <textarea class="form-control textarea-resize" name="notes">{{ @$kart_veri->d8_notes }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-primary">Geri Al</button>
            </div>
        </div>
    </div>
</div>

    <style>
        :root {
            --primary-dark: #1e293b;
            --primary-blue: #2563eb;
            --primary-blue-dark: #1d4ed8;
            --secondary-gray: #64748b;
            --background-gray: #f8fafc;
            --border-gray: #e2e8f0;
            --warning-red: #dc2626;
            --warning-bg: #fef2f2;
            --success-green: #16a34a;
            --card-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --modal-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9998;
            padding: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--modal-shadow);
            width: 100%;
            max-width: 1100px;
            max-height: 85vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: slideUp 0.4s ease-out;
        }

        .modal-header {
            padding: 28px 32px 24px;
            border-bottom: 1px solid var(--border-gray);
            background: linear-gradient(to bottom, #ffffff, #fafbfc);
        }

        .modal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: var(--primary-blue);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .badge-icon {
            width: 12px;
            height: 12px;
            border: 2px solid var(--primary-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-icon::before {
            content: '✓';
            font-size: 8px;
            font-weight: bold;
        }

        .modal-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .modal-subtitle {
            font-size: 14px;
            color: var(--secondary-gray);
            line-height: 1.6;
        }

        .modal-body {
            padding: 24px 32px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: var(--background-gray);
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: var(--border-gray);
            border-radius: 3px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-gray);
        }

        .checklist-item {
            background: white;
            border: 1px solid var(--border-gray);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.2s ease;
        }

        .checklist-item:hover {
            border-color: #cbd5e1;
            box-shadow: var(--card-shadow);
        }

        .checklist-item.answered-yes {
            background: linear-gradient(to right, #f0fdf4, #ffffff);
            border-color: #86efac;
        }

        .checklist-item.answered-no {
            background: linear-gradient(to right, #fef2f2, #ffffff);
            border-color: #fca5a5;
        }

        .question-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .question-number {
            background: var(--primary-dark);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
        }

        .question-text {
            flex: 1;
            font-size: 15px;
            font-weight: 600;
            color: var(--primary-dark);
            line-height: 1.5;
            padding-top: 2px;
        }

        .answer-options {
            display: flex;
            gap: 12px;
            margin-left: 40px;
        }

        .radio-option {
            position: relative;
            flex: 1;
        }

        .radio-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .radio-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border: 2px solid var(--border-gray);
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 600;
            color: var(--secondary-gray);
            user-select: none;
        }

        .radio-option input[type="radio"]:checked + .radio-label {
            border-color: var(--primary-blue);
            background: var(--primary-blue);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
        }

        .radio-option.no input[type="radio"]:checked + .radio-label {
            border-color: var(--warning-red);
            background: var(--warning-red);
        }

        .radio-label:hover {
            border-color: #cbd5e1;
            background: var(--background-gray);
        }

        .radio-option input[type="radio"]:checked + .radio-label:hover {
            background: var(--primary-blue-dark);
            border-color: var(--primary-blue-dark);
        }

        .radio-option.no input[type="radio"]:checked + .radio-label:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .radio-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .radio-option input[type="radio"]:checked + .radio-label .radio-icon {
            background: white;
        }

        .radio-option input[type="radio"]:checked + .radio-label .radio-icon::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-blue);
        }

        .radio-option.no input[type="radio"]:checked + .radio-label .radio-icon::before {
            background: var(--warning-red);
        }

        .warning-message {
            margin: 16px 0 0 40px;
            padding: 12px 16px;
            background: var(--warning-bg);
            border-left: 3px solid var(--warning-red);
            border-radius: 6px;
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .warning-message.show {
            display: block;
        }

        .warning-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--warning-red);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .warning-text {
            font-size: 13px;
            color: #7f1d1d;
            line-height: 1.5;
        }

        .explanation-input {
            margin-top: 8px;
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            resize: vertical;
            min-height: 60px;
            transition: border-color 0.2s ease;
        }

        .explanation-input:focus {
            outline: none;
            border-color: var(--warning-red);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .modal-footer {
            padding: 20px 32px;
            border-top: 1px solid var(--border-gray);
            background: var(--background-gray);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--secondary-gray);
        }

        .progress-count {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--primary-dark);
        }

        .progress-bar {
            width: 100px;
            height: 6px;
            background: var(--border-gray);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(to right, var(--primary-blue), var(--success-green));
            border-radius: 3px;
            transition: width 0.3s ease;
            width: 0%;
        }

        .submit-button {
            background: var(--secondary-gray);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: not-allowed;
            transition: all 0.3s ease;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0.6;
        }

        .submit-button.active {
            background: linear-gradient(135deg, var(--primary-blue), #1d4ed8);
            cursor: pointer;
            opacity: 1;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .submit-button.active:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }

        .submit-button.active:active {
            transform: translateY(0);
        }

        .button-icon {
            font-size: 18px;
        }

        @media (max-width: 640px) {
            .modal-container {
                max-height: 95vh;
            }

            .modal-header {
                padding: 20px 20px 16px;
            }

            .modal-body {
                padding: 16px 20px;
            }

            .modal-footer {
                padding: 16px 20px;
                flex-direction: column;
                align-items: stretch;
            }

            .modal-title {
                font-size: 19px;
            }

            .checklist-item {
                padding: 16px;
            }

            .answer-options {
                margin-left: 0;
                margin-top: 12px;
            }

            .question-header {
                flex-direction: column;
                gap: 8px;
            }

            .warning-message {
                margin-left: 0;
            }

            .submit-button {
                width: 100%;
                justify-content: center;
            }

            .progress-indicator {
                width: 100%;
                justify-content: center;
            }
        }

        /* Demo için arka plan içeriği */
        .demo-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 1;
            opacity: 0.3;
        }

        .demo-content h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .demo-content p {
            font-size: 18px;
        }
    </style>
    <!-- Zorunlu Modal -->
    <div class="modal-overlay" id="checklistModal">
        <div class="modal-container">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <div class="modal-badge">
                    <span class="badge-icon"></span>
                    <span>Zorunlu Kontrol</span>
                </div>
                <H5 class="modal-title">Devam etmeden önce lütfen aşağıdaki kontrolleri tamamlayın</H5>
            </div>

            <div class="modal-body" id="checkBody">

                <!-- Soru 1 -->
                <div class="checklist-item" data-question="1">
                    <div class="question-header">
                        <div class="question-number">01</div>
                        <div class="question-text">Evrak bilgileri kontrol edildi mi?</div>
                    </div>
                    <div class="answer-options">
                        <div class="radio-option yes">
                            <input type="radio" name="evrak" value="EVET" id="evrak-yes">
                            <label for="evrak-yes" class="radio-label">
                                <span class="radio-icon"></span>
                                <span>Evet</span>
                            </label>
                        </div>
                        <div class="radio-option no">
                            <input type="radio" name="evrak" value="HAYIR" id="evrak-no">
                            <label for="evrak-no" class="radio-label">
                                <span class="radio-icon"></span>
                                <span>Hayır</span>
                            </label>
                        </div>
                    </div>
                    <div class="warning-message">
                        <div class="warning-title">⚠ Dikkat Gerekli</div>
                        <div class="warning-text">Evrak bilgileri kontrol edilmeden devam edilemez. Lütfen evrakları kontrol edip işaretleyin.</div>
                        <textarea class="explanation-input" placeholder="Açıklama giriniz (opsiyonel)..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="progress-indicator">
                    <span class="progress-count">0/4</span>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
                <button class="submit-button" id="submitButton" disabled>
                    <span>Kaydet ve Devam Et</span>
                    <span class="button-icon">→</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        (function($) {
            'use strict';

            // ============================================
            // CONFIGURATION
            // ============================================

            const CONFIG = {
                selectors: {
                    modal: '#checklistModal',
                    submitButton: '#submitButton',
                    progressFill: '.progress-fill',
                    progressCount: '.progress-count',
                    modalBody: '.modal-body',
                    modalContainer: '.modal-container',
                    checklistItem: '.checklist-item',
                    warningMessage: '.warning-message',
                    radioInput: 'input[type="radio"]',
                    textareaInput: 'textarea.explanation-input'
                },
                animations: {
                    fadeOut: 'fadeOut 0.3s ease-out',
                    shake: 'shake 0.5s ease-in-out',
                    fadeIn: 'fadeIn 0.3s ease-out'
                }
            };

            // ============================================
            // STATE MANAGEMENT
            // ============================================

            const state = {
                answeredCount: 0,
                answers: {},
                explanations: {}
            };

            // ============================================
            // CACHE DOM ELEMENTS
            // ============================================

            const $modal = $(CONFIG.selectors.modal);
            const $submitButton = $(CONFIG.selectors.submitButton);
            const $progressFill = $(CONFIG.selectors.progressFill);
            const $progressCount = $(CONFIG.selectors.progressCount);
            const $modalBody = $(CONFIG.selectors.modalBody);
            const $modalContainer = $(CONFIG.selectors.modalContainer);
            const totalQuestions = $(CONFIG.selectors.checklistItem).length;

            // ============================================
            // CORE FUNCTIONS
            // ============================================

            /**
            * İlerleme durumunu güncelle
            */
            function updateProgress() {
                const progress = (state.answeredCount / totalQuestions) * 100;
                
                $progressFill.css('width', progress + '%');
                $progressCount.text(`${state.answeredCount}/${totalQuestions}`);
                
                // Tüm sorular cevaplanmışsa butonu aktif et
                if (state.answeredCount === totalQuestions) {
                    $submitButton
                        .prop('disabled', false)
                        .addClass('active');
                } else {
                    $submitButton
                        .prop('disabled', true)
                        .removeClass('active');
                }
            }

            /**
            * Modal shake animasyonu
            */
            function shakeModal() {
                $modalContainer
                    .css('animation', CONFIG.animations.shake)
                    .one('animationend', function() {
                        $(this).css('animation', '');
                    });
            }

            /**
            * Radio button değişikliğini işle
            * @param {jQuery} $radio - Radio button jQuery objesi
            */
            function handleRadioChange($radio) {
                const $checklistItem = $radio.closest(CONFIG.selectors.checklistItem);
                const $warningMessage = $checklistItem.find(CONFIG.selectors.warningMessage);
                const questionName = $radio.attr('name');
                const questionValue = $radio.val();
                
                // State'i güncelle
                const wasAnswered = state.answers.hasOwnProperty(questionName);
                state.answers[questionName] = questionValue;
                
                if (!wasAnswered) {
                    state.answeredCount++;
                }
                
                // Visual feedback
                $checklistItem.removeClass('answered-yes answered-no');
                
                if (questionValue === 'EVET') {
                    $checklistItem.addClass('answered-yes');
                    $warningMessage.removeClass('show');
                    
                    // EVET seçilirse açıklamayı state'den kaldır
                    const questionNum = $checklistItem.data('question');
                    delete state.explanations[`question_${questionNum}`];
                } else {
                    $checklistItem.addClass('answered-no');
                    $warningMessage.addClass('show');
                }
                
                updateProgress();
            }

            /**
            * Textarea değişikliğini işle
            * @param {jQuery} $textarea - Textarea jQuery objesi
            */
            function handleTextareaInput($textarea) {
                const $questionItem = $textarea.closest(CONFIG.selectors.checklistItem);
                const questionNum = $questionItem.data('question');
                const value = $textarea.val().trim();
                
                if (value) {
                    state.explanations[`question_${questionNum}`] = value;
                } else {
                    delete state.explanations[`question_${questionNum}`];
                }
            }

            /**
            * Form submit işlemi
            */
            function handleSubmit() {
                if (!$submitButton.hasClass('active')) {
                    return;
                }

                console.log('Form Data:', state.answers);
                console.log('Explanations:', state.explanations);
                
                // Modal'ı kapat
                $modal.css('animation', CONFIG.animations.fadeOut);
                
                setTimeout(function() {
                    $modal.hide();
                    
                    // Eğer mesaj fonksiyonu varsa kullan (SweetAlert, Toastr vs.)
                    if (typeof mesaj === 'function') {
                        mesaj('Teşekkürler', 'success');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı',
                            text: 'Kontrol listesi tamamlandı'
                        });
                    } else {
                        console.log('✓ Kontrol listesi başarıyla tamamlandı');
                    }
                    
                    // AJAX ile veri gönderimi (opsiyonel)
                    submitToServer();
                    
                }, 300);
            }

            /**
            * Server'a veri gönder (AJAX)
            */
            function submitToServer() {
                // AJAX endpoint'i varsa kullan
                const ajaxEndpoint = $modal.data('ajax-url') || 'submit-checklist.php';
                
                $.ajax({
                    url: ajaxEndpoint,
                    method: 'POST',
                    data: {
                        checklist: state.answers,
                        explanations: state.explanations,
                        timestamp: new Date().toISOString()
                    },
                    beforeSend: function() {
                        console.log('📤 Veri gönderiliyor...');
                    },
                    success: function(response) {
                        console.log('✓ Başarılı:', response);
                        
                        // Başarılı callback varsa çağır
                        if (typeof window.onChecklistSuccess === 'function') {
                            window.onChecklistSuccess(response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('✗ Hata:', error);
                        
                        // Hata callback varsa çağır
                        if (typeof window.onChecklistError === 'function') {
                            window.onChecklistError(error);
                        }
                    }
                });
            }

            // ============================================
            // EVENT DELEGATION - jQuery ile temiz kod
            // ============================================

            /**
            * Radio button değişikliği (EVENT DELEGATION)
            */
            $modalBody.on('change', CONFIG.selectors.radioInput, function() {
                handleRadioChange($(this));
            });

            /**
            * Textarea input (EVENT DELEGATION)
            */
            $modalBody.on('input', CONFIG.selectors.textareaInput, function() {
                handleTextareaInput($(this));
            });

            /**
            * Submit button click
            */
            $submitButton.on('click', handleSubmit);

            /**
            * Modal overlay click (dışına tıklama)
            */
            $modal.on('click', function(e) {
                if ($(e.target).is(CONFIG.selectors.modal)) {
                    e.preventDefault();
                    shakeModal();
                }
            });

            /**
            * Keyboard eventleri
            */
            $(document).on('keydown', function(e) {
                // Modal görünür değilse çık
                if (!$modal.is(':visible')) return;

                // ESC tuşunu engelle
                if (e.key === 'Escape') {
                    e.preventDefault();
                    shakeModal();
                }
                
                // Enter tuşu ile submit (textarea'da değilse)
                if (e.key === 'Enter' && 
                    $submitButton.hasClass('active') && 
                    !$(e.target).is('textarea')) {
                    e.preventDefault();
                    handleSubmit();
                }
            });

            // ============================================
            // PUBLIC API - Window objesine ekle
            // ============================================

            window.ChecklistModal = {
                /**
                * Mevcut state'i al
                * @returns {Object} State objesi
                */
                getState: function() {
                    return $.extend(true, {}, state);
                },

                /**
                * Formu sıfırla
                */
                reset: function() {
                    state.answeredCount = 0;
                    state.answers = {};
                    state.explanations = {};
                    
                    $(CONFIG.selectors.radioInput + ':checked').prop('checked', false);
                    $(CONFIG.selectors.checklistItem).removeClass('answered-yes answered-no');
                    $(CONFIG.selectors.warningMessage).removeClass('show');
                    $('textarea').val('');
                    
                    updateProgress();
                    
                    console.log('✓ Form sıfırlandı');
                },

                /**
                * Modalı göster
                */
                show: function() {
                    $modal.show().css('animation', CONFIG.animations.fadeIn);
                    console.log('✓ Modal gösterildi');
                },

                /**
                * Modalı kapat
                */
                hide: function() {
                    handleSubmit();
                },

                /**
                * Belirli bir sorunun cevabını al
                * @param {string} questionName - Soru adı (name attribute)
                * @returns {string|null} Cevap
                */
                getAnswer: function(questionName) {
                    return state.answers[questionName] || null;
                },

                /**
                * Tüm cevapları al (PHP'ye göndermek için)
                * @returns {Object} Form data
                */
                getFormData: function() {
                    return {
                        answers: state.answers,
                        explanations: state.explanations,
                        completed: state.answeredCount === totalQuestions,
                        totalQuestions: totalQuestions,
                        answeredCount: state.answeredCount
                    };
                },

                /**
                * Belirli bir soruyu programatik olarak cevapla
                * @param {string} questionName - Soru adı
                * @param {string} value - EVET veya HAYIR
                */
                setAnswer: function(questionName, value) {
                    const $radio = $(`input[name="${questionName}"][value="${value}"]`);
                    if ($radio.length) {
                        $radio.prop('checked', true).trigger('change');
                    }
                },

                /**
                * Validation kontrolü
                * @returns {boolean} Tüm sorular cevaplanmış mı?
                */
                isComplete: function() {
                    return state.answeredCount === totalQuestions;
                },

                /**
                * Debug bilgisi göster
                */
                debug: function() {
                    console.log('=== CHECKLIST DEBUG ===');
                    console.log('Total Questions:', totalQuestions);
                    console.log('Answered:', state.answeredCount);
                    console.log('Answers:', state.answers);
                    console.log('Explanations:', state.explanations);
                    console.log('Complete:', this.isComplete());
                    console.log('=======================');
                }
            };

            // ============================================
            // INITIALIZATION
            // ============================================

            $(document).ready(function() {
                console.log('✓ Checklist Modal hazır');
                console.log('API Methods:', Object.keys(window.ChecklistModal).join(', '));
            });

        })(jQuery);
    </script>
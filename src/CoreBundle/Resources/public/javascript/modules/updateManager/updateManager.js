if (typeof CHAMELEON === "undefined" || !CHAMELEON) {
    var CHAMELEON = {}; // Remove dependency from cms.js being loaded first
}

CHAMELEON.UPDATE_MANAGER = {
    config: {
        sRunUpdatesButtonId: '#btnRunUpdates',
        sGoBackButtonId: '#btnGoBack',
        text: {
            selectFile: '',
            progressBarRunning: '',
            progressBarFinished: '',
            successfulQueriesShow: '',
            successfulQueriesHide: ''
        }
    },
    _ajaxUrl: '',

    updatesByBundle: null,

    _fProgressBarPercentPerUpdate: 0,
    _fProgressBarPercent: 0,

    _aPostUpdateCommands: [],

    countTotal: 0,
    countPending: 0,
    countProcessed: 0,
    countExecuted: 0,
    countSkipped: 0,

    initSingleUpdate: function()
    {
        var self = this,
            availableUpdateFiles = null,
            $singleUpdateSelectBundle = $('#singleUpdateSelectBundle'),
            $singleUpdateSelectFile = $('#singleUpdateSelectFile'),
            $buttonSetUpdate = $('#btnSetUpdate');

        $singleUpdateSelectBundle.bind('change', function() {
            $singleUpdateSelectFile.unbind().attr('disabled', 'disabled');
            if($(this).val() != 'NULL'){
                var selectedBundle = $(this).find(':selected').text();
                availableUpdateFiles = self.updatesByBundle[selectedBundle];

                for (var i = 0; i < availableUpdateFiles.length; i++) {
                    var updateFile = availableUpdateFiles[i];
                    $singleUpdateSelectFile.append('<option value="' + i + '">' + updateFile.fileName + '</option>');
                }

                $singleUpdateSelectFile.find('option:first').text(self.config.text.selectFile);

                $singleUpdateSelectFile.removeAttr('disabled').bind('change', function(){
                    $buttonSetUpdate.unbind();
                    if($(this).val() != 'NULL'){
                        var _selectedUpdateFileIndex = $(this).val();

                        $buttonSetUpdate.removeClass('disabled').bind('click', function(){

                            $('#singleUpdateSelectBundle, #singleUpdateSelectSubdir, #singleUpdateSelectFile').attr('disabled', 'disabled');
                            $(this).addClass('disabled');
                            $(self.config.sRunUpdatesButtonId).removeClass('disabled');

                            var updatesToExecute = {};

                            updatesToExecute[selectedBundle] = [];
                            updatesToExecute[selectedBundle].push(availableUpdateFiles[_selectedUpdateFileIndex]);

                            self.setUpdateFiles(updatesToExecute);
                            self.init();

                            $(this).unbind();
                        });
                    }
                });
            }
        });
    },

    init: function ()
    {
        if (this.updatesByBundle == null) {
            alert('Please set "updatesByBundle" with "setUpdateFiles()" first');
        } else {
            for (var bundleName in this.updatesByBundle) {
                var updateCount = this.updatesByBundle[bundleName].length;
                this.countTotal += updateCount;
                this.countPending += updateCount;
                for (var i = 0; i < updateCount; i++) {
                    this.updatesByBundle[bundleName][i].processed = false;
                }
            }

            this._updateGuiCount();
            this.bindListeners();

            this._ajaxUrl = _cms_controler + '?';
            this._ajaxUrl += 'pagedef=CMSUpdateManager&';
            this._ajaxUrl += 'module_fnc[contentmodule]=ExecuteAjaxCall&';
            this._ajaxUrl += _cmsauthenticitytoken_parameter;
        }
    },
    setConfig: function (config)
    {
        $.extend(this.config, config);
    },
    setUpdateFiles: function (updateFiles)
    {
        this.updatesByBundle = updateFiles;
    },
    addUpdateSuccessQueries: function (currentUpdate, successQueries, renderedSuccessQueriesUpdate)
    {
        var self = this;
        this._getMessageContainerForUpdate(currentUpdate).find('.runFilesInfo').append(renderedSuccessQueriesUpdate);
    },
    addUpdateErrors: function (currentUpdate, errorQueries, renderedErrorQueriesGlobal, renderedErrorQueriesUpdate)
    {
        var $globalErrorListContainer = $('#error-list'),
            $globalErrorCountContainer = $('#count-errors');

        $globalErrorListContainer.append(renderedErrorQueriesGlobal);

        this._getMessageContainerForUpdate(currentUpdate).find('.runFilesInfo').append(renderedErrorQueriesUpdate);

        $globalErrorCountContainer.text(parseInt($globalErrorCountContainer.text()) + errorQueries.length);
        $('#updateErrorContainer').removeClass('d-none');
    },
    addUpdateInfoMessages: function(currentUpdate, messages, renderedMessagesGlobal, renderedMessagesUpdate)
    {
        var $globalInfoListContainer = $('#info-list'),
            $globalInfoCountContainer = $('#count-info');

        $globalInfoListContainer.append(renderedMessagesGlobal);
        this._getMessageContainerForUpdate(currentUpdate).find('.runFilesInfo').append(renderedMessagesUpdate);

        $globalInfoCountContainer.text(parseInt($globalInfoCountContainer.text()) + messages.length);
        $('#updateInfoContainer').removeClass('d-none');
    },
    _getMessageContainerForUpdate: function(currentUpdate)
    {
        var $container = $('#update-' + currentUpdate.bundleName + '-' + currentUpdate.buildNumber);
        if ($container.length > 0) {
            return $container;
        }

        var $updateManagerOutput = $('#updateManagerOutput');
        $updateManagerOutput.find('.body').append(
            '<div id="update-' + currentUpdate.bundleName + '-' + currentUpdate.buildNumber + '">' +
                '<div class="card card-accent-info mb-3">\n' +
                '  <div class="card-header fileInfo"></div>\n' +
                '  <div class="card-body"><div class="callout callout-info updateBody"></div><div class="runFilesInfo"></div>\n' +
                '  </div>\n' +
              '  </div>\n' +
            '</div>'
        );
        $updateManagerOutput.removeClass('d-none');

        return $('#update-' + currentUpdate.bundleName + '-' + currentUpdate.buildNumber);
    },
    bindListeners: function ()
    {
        var self = this;

        $(this.config.sRunUpdatesButtonId).bind('click', function (event) {
            event.preventDefault();
            $('#ajaxTimeoutSelect').attr('disabled', 'disabled');

            $(this).attr("disabled", "disabled");
            $(self.config.sGoBackButtonId).bind('click',function (event) {
                event.preventDefault();
                return false;
            }).addClass("disabled");

            self._runUpdates();
        });
    },
    _runUpdates: function ()
    {
        var self = this;
        this._addGuiTypeContainer();
        this._calculateProgressBarStep();

        // set the ajax timeout
        $.ajaxSetup({
            timeout: (1000 * 60 * $('#ajaxTimeoutSelect').val())
        });

        // wait some seconds so the DOM can update
        setTimeout(function () {
            // start the update with element 0
            self._runSingleFileUpdate(null, 0)
        }, 500);

    },
    _calculateProgressBarStep: function ()
    {
        this._fProgressBarPercentPerUpdate = 100 / this.countTotal;

    },
    _incrementProgressBarByUpdate: function ()
    {
        if ((this._fProgressBarPercent + this._fProgressBarPercentPerUpdate) <= 100.1) {
            this._fProgressBarPercent += this._fProgressBarPercentPerUpdate;
            $('#updateProgressBar').css('width', this._fProgressBarPercent + '%');
            $('#updateProgressBarText').text(this.config.text.progressBarRunning + ' (' + Math.round(this._fProgressBarPercent) + ' %)');
        }
    },
    _addGuiTypeContainer: function ()
    {
        if (this.countTotal > 0) {
            $('#updateManagerOutput').append(
            '<div class="body card-body">' +
            '</div>');
        }
    },
    _getUpdateFileIndexFromBuildNumber: function (bundleName, buildNumber)
    {
        for (var i = 0; i < this.updatesByBundle[bundleName].length; i++) {
            if(this.updatesByBundle[bundleName][i].buildNumber == buildNumber) {
                return i;
            }
        }

        return false;
    },
    addProcessedUpdate: function (bundleName, buildNumber, updateFileResponse)
    {
        var self = this,
            updateFileIndex = this._getUpdateFileIndexFromBuildNumber(bundleName, buildNumber),
            updateToProcess = this.updatesByBundle[bundleName][updateFileIndex];

        if (!updateToProcess.processed) {
            updateToProcess.processed = true;

            setTimeout(function () {
                self._processUpdateResponse(updateToProcess, updateFileResponse);
            }, 500);
        }
    },
    _runSingleFileUpdate: function (bundleName, iUpdateFileIndex)
    {
        var self = this;

        if (null == bundleName || iUpdateFileIndex >= this.updatesByBundle[bundleName].length) {
            bundleName = this._getNextBundleToExecute(bundleName);
            iUpdateFileIndex = 0;
        }
        if (null === bundleName) {
            // all updates are finished
            this._executePostUpdateCommands();

            return;
        }

        var currentUpdate = this.updatesByBundle[bundleName][iUpdateFileIndex];

        if (currentUpdate.processed == false) {
            $.ajax({
                url: this._ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    _fnc: "runSingleUpdate",
                    bundleName: currentUpdate.bundleName,
                    fileName: currentUpdate.fileName
                },
                success: function (data) {
                    var dbCounterName = data.dbCounterName,
                        bundleName = currentUpdate.bundleName,
                        fileName = currentUpdate.fileName;

                    if (data.responseStatus == 'SUCCESS') {
                        self._processUpdateResponse(currentUpdate, data);
                        currentUpdate.processed = true;
                        iUpdateFileIndex++;
                        self._runSingleFileUpdate(bundleName, iUpdateFileIndex);
                    } else {
                        console.log('------------- RESPONSE ERROR');
                        console.log('requested [' + iUpdateFileIndex + ']: ' + currentUpdate.bundleName + '[' + currentUpdate.indexOriginal + '] | ' + currentUpdate.fileName);
                        console.log(data);
                        console.log('------------- /RESPONSE ERROR ------------------');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    self.addFatalError(currentUpdate, jqXHR, textStatus, errorThrown);
                }
            });
        } else {
            /*
             * file has been processed before (internally in another update)
             * -> increase the index and go to the next update in the list
             */
            iUpdateFileIndex++;
            self._runSingleFileUpdate(bundleName, iUpdateFileIndex);
        }
    },
    _getNextBundleToExecute: function(lastBundleName)
    {
        var found = false;
        for (var bundleName in this.updatesByBundle) {
            if (null == lastBundleName) {
                return bundleName;
            }
            if (bundleName == lastBundleName) {
                found = true;
                continue;
            }
            if (false == found) {
                continue;
            }

            return bundleName;
        }

        return null;
    },
    _executePostUpdateCommands: function()
    {
        this._executePostUpdateCommand(0);
    },
    _executePostUpdateCommand: function(iIndex)
    {
        var self = this;

        if(iIndex in this._aPostUpdateCommands){
            $('#updateProgressBarText').text(this._aPostUpdateCommands[iIndex].sMessage);

            $.ajax({
                type: 'POST',
                data: {
                    '_fnc': this._aPostUpdateCommands[iIndex].sCommand
                },
                url: this._ajaxUrl,
                success: function(data){
                    iIndex++;
                    self._executePostUpdateCommand(iIndex);
                },
                error: function(){

                }
            });
        }
        else {
            this._activateGuiElements();
        }
    },
    _activateGuiElements: function()
    {
        $(this.config.sRunUpdatesButtonId).unbind();
        $(this.config.sGoBackButtonId).unbind().removeClass('disabled');
        $('#updateProgressBarText').text(this.config.text.progressBarFinished);
    },
    addPostUpdateCommand: function(sCommand, sMessage)
    {
        this._aPostUpdateCommands.push({
            sCommand: sCommand,
            sMessage: sMessage
        })
    },
    _processUpdateResponse: function (currentUpdate, data)
    {
        this._updateGuiCount(data.updateStatus);
        this._incrementProgressBarByUpdate();

        if (data.updateStatus == 'executed') {
            var $messageContainerForUpdate = this._getMessageContainerForUpdate(currentUpdate);
            $messageContainerForUpdate.find('.fileInfo').html(currentUpdate.fileName);
            $messageContainerForUpdate.find('.updateBody').html(data.fileContents);
        }
        if (typeof data.exceptions !== 'undefined' && data.exceptions.length > 0) {
            this.addUpdateErrors(currentUpdate, data.exceptions, data.renderedExceptionsGlobal, data.renderedExceptionsUpdate);
        }
        if (typeof data.errorQueries !== 'undefined' && data.errorQueries.length > 0) {
            this.addUpdateErrors(currentUpdate, data.errorQueries, data.renderedErrorQueriesGlobal, data.renderedErrorQueriesUpdate);
        }
        if (typeof data.infoMessages !== 'undefined' && data.infoMessages.length > 0) {
            this.addUpdateInfoMessages(currentUpdate, data.infoMessages, data.renderedInfoMessagesGlobal, data.renderedInfoMessagesUpdate);
        }
        if (typeof data.successQueries !== 'undefined' && data.successQueries.length > 0) {
            this.addUpdateSuccessQueries(currentUpdate, data.successQueries, data.renderedSuccessQueriesUpdate);
        }
    },
    addFatalError: function (currentUpdate, oError, textStatus, errorThrown)
    {
        var $globalFatalErrorContainer = $('#updateFatalErrorContainer');

        $globalFatalErrorContainer.find('#updateFatalError').append(
            '<p>file: ' + currentUpdate.fileName + '</p>' +
            '<p>error thrown: ' + errorThrown + '</p>' +
            '<p>response status: ' + oError.status + '</p>' +
            '<p>response text: ' + oError.responseText + '</p>'
        );
        $globalFatalErrorContainer.removeClass('d-none');
        this._activateGuiElements();
    },
    _updateGuiCount: function (sStatus)
    {
        if (typeof sStatus != 'undefined') {
            this.countPending--;
            this.countProcessed++;

            if (sStatus == 'executed') {
                this.countExecuted++;
            } else if (sStatus == 'skipped') {
                this.countSkipped++;
            }
        }

        var $updateCountInfo = $('#updateCountInfo');
        $updateCountInfo.find('.count-total').text(this.countTotal);
        $updateCountInfo.find('.count-upcoming').text(this.countPending);
        $updateCountInfo.find('.count-processed').text(this.countProcessed);
        $updateCountInfo.find('.count-executed').text(this.countExecuted);
        $updateCountInfo.find('.count-skipped').text(this.countSkipped);
    }
};

(function (window) {
    //¼æÈÝ
    window.URL = window.URL || window.webkitURL;
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;


    var HZRecorder = function (stream, config) {
        config = config || {};
        config.sampleBits = config.sampleBits || 16;      //²ÉÑùÊýÎ» 8, 16
        config.sampleRate = config.sampleRate || (8000);   //²ÉÑùÂÊ(1/6 44100)


        var context = new AudioContext();
        var audioInput = context.createMediaStreamSource(stream);
        var recorder = context.createScriptProcessor(4096, 1, 1);


        var audioData = {
            size: 0          //Â¼ÒôÎÄ¼þ³¤¶È
            , buffer: []     //Â¼Òô»º´æ
            , inputSampleRate: context.sampleRate    //ÊäÈë²ÉÑùÂÊ
            , inputSampleBits: 16       //ÊäÈë²ÉÑùÊýÎ» 8, 16
            , outputSampleRate: config.sampleRate    //Êä³ö²ÉÑùÂÊ
            , oututSampleBits: config.sampleBits       //Êä³ö²ÉÑùÊýÎ» 8, 16
            , input: function (data) {
                this.buffer.push(new Float32Array(data));
                this.size += data.length;
            }
            , compress: function () { //ºÏ²¢Ñ¹Ëõ
                //ºÏ²¢
                var data = new Float32Array(this.size);
                var offset = 0;
                for (var i = 0; i < this.buffer.length; i++) {
                    data.set(this.buffer[i], offset);
                    offset += this.buffer[i].length;
                }
                //Ñ¹Ëõ
                var compression = parseInt(this.inputSampleRate / this.outputSampleRate);
                var length = data.length / compression;
                var result = new Float32Array(length);
                var index = 0, j = 0;
                while (index < length) {
                    result[index] = data[j];
                    j += compression;
                    index++;
                }
                return result;
            }
            , encodeWAV: function () {
                var sampleRate = Math.min(this.inputSampleRate, this.outputSampleRate);
                var sampleBits = Math.min(this.inputSampleBits, this.oututSampleBits);
                var bytes = this.compress();
                var dataLength = bytes.length * (sampleBits / 8);
                var buffer = new ArrayBuffer(44 + dataLength);
                var data = new DataView(buffer);


                var channelCount = 1;//µ¥ÉùµÀ
                var offset = 0;


                var writeString = function (str) {
                    for (var i = 0; i < str.length; i++) {
                        data.setUint8(offset + i, str.charCodeAt(i));
                    }
                }
                
                // ×ÊÔ´½»»»ÎÄ¼þ±êÊ¶·û 
                writeString('RIFF'); offset += 4;
                // ÏÂ¸öµØÖ·¿ªÊ¼µ½ÎÄ¼þÎ²×Ü×Ö½ÚÊý,¼´ÎÄ¼þ´óÐ¡-8 
                data.setUint32(offset, 36 + dataLength, true); offset += 4;
                // WAVÎÄ¼þ±êÖ¾
                writeString('WAVE'); offset += 4;
                // ²¨ÐÎ¸ñÊ½±êÖ¾ 
                writeString('fmt '); offset += 4;
                // ¹ýÂË×Ö½Ú,Ò»°ãÎª 0x10 = 16 
                data.setUint32(offset, 16, true); offset += 4;
                // ¸ñÊ½Àà±ð (PCMÐÎÊ½²ÉÑùÊý¾Ý) 
                data.setUint16(offset, 1, true); offset += 2;
                // Í¨µÀÊý 
                data.setUint16(offset, channelCount, true); offset += 2;
                // ²ÉÑùÂÊ,Ã¿ÃëÑù±¾Êý,±íÊ¾Ã¿¸öÍ¨µÀµÄ²¥·ÅËÙ¶È 
                data.setUint32(offset, sampleRate, true); offset += 4;
                // ²¨ÐÎÊý¾Ý´«ÊäÂÊ (Ã¿ÃëÆ½¾ù×Ö½ÚÊý) µ¥ÉùµÀ¡ÁÃ¿ÃëÊý¾ÝÎ»Êý¡ÁÃ¿Ñù±¾Êý¾ÝÎ»/8 
                data.setUint32(offset, channelCount * sampleRate * (sampleBits / 8), true); offset += 4;
                // ¿ìÊý¾Ýµ÷ÕûÊý ²ÉÑùÒ»´ÎÕ¼ÓÃ×Ö½ÚÊý µ¥ÉùµÀ¡ÁÃ¿Ñù±¾µÄÊý¾ÝÎ»Êý/8 
                data.setUint16(offset, channelCount * (sampleBits / 8), true); offset += 2;
                // Ã¿Ñù±¾Êý¾ÝÎ»Êý 
                data.setUint16(offset, sampleBits, true); offset += 2;
                // Êý¾Ý±êÊ¶·û 
                writeString('data'); offset += 4;
                // ²ÉÑùÊý¾Ý×ÜÊý,¼´Êý¾Ý×Ü´óÐ¡-44 
                data.setUint32(offset, dataLength, true); offset += 4;
                // Ð´Èë²ÉÑùÊý¾Ý 
                if (sampleBits === 8) {
                    for (var i = 0; i < bytes.length; i++, offset++) {
                        var s = Math.max(-1, Math.min(1, bytes[i]));
                        var val = s < 0 ? s * 0x8000 : s * 0x7FFF;
                        val = parseInt(255 / (65535 / (val + 32768)));
                        data.setInt8(offset, val, true);
                    }
                } else {
                    for (var i = 0; i < bytes.length; i++, offset += 2) {
                        var s = Math.max(-1, Math.min(1, bytes[i]));
                        data.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
                    }
                }


                return new Blob([data], { type: 'audio/wav' });
            }
        };


        //¿ªÊ¼Â¼Òô
        this.start = function () {
            audioInput.connect(recorder);
            recorder.connect(context.destination);
        }


        //Í£Ö¹
        this.stop = function () {
            recorder.disconnect();
        }


        //»ñÈ¡ÒôÆµÎÄ¼þ
        this.getBlob = function () {
            this.stop();
            return audioData.encodeWAV();
        }


        //»Ø·Å
        this.play = function (audio) {
            audio.src = window.URL.createObjectURL(this.getBlob());
        }


        //ÉÏ´«
        this.upload = function (url, callback) {
            var fd = new FormData();
            fd.append("audioData", this.getBlob());
            var xhr = new XMLHttpRequest();
            if (callback) {
                xhr.upload.addEventListener("progress", function (e) {
                    callback('uploading', e);
                }, false);
                xhr.addEventListener("load", function (e) {
                    callback('ok', e);
                }, false);
                xhr.addEventListener("error", function (e) {
                    callback('error', e);
                }, false);
                xhr.addEventListener("abort", function (e) {
                    callback('cancel', e);
                }, false);
            }
            xhr.open("POST", url);
            xhr.send(fd);
        }


        //ÒôÆµ²É¼¯
        recorder.onaudioprocess = function (e) {
            audioData.input(e.inputBuffer.getChannelData(0));
            //record(e.inputBuffer.getChannelData(0));
        }


    };
    //Å×³öÒì³£
    HZRecorder.throwError = function (message) {
        alert(message);
        throw new function () { this.toString = function () { return message; } }
    }
    //ÊÇ·ñÖ§³ÖÂ¼Òô
    HZRecorder.canRecording = (navigator.getUserMedia != null);
    //»ñÈ¡Â¼Òô»ú
    HZRecorder.get = function (callback, config) {
        if (callback) {
            if (navigator.getUserMedia) {
                navigator.getUserMedia(
                    { audio: true } //Ö»ÆôÓÃÒôÆµ
                    , function (stream) {
                        var rec = new HZRecorder(stream, config);
                        callback(rec);
                    }
                    , function (error) {
                        switch (error.code || error.name) {
                            case 'PERMISSION_DENIED':
                            case 'PermissionDeniedError':
                                HZRecorder.throwError('用户拒绝提供信息.');
                                break;
                            case 'NOT_SUPPORTED_ERROR':
                            case 'NotSupportedError':
                                HZRecorder.throwError('浏览器不支持硬件设备');
                                break;
                            case 'MANDATORY_UNSATISFIED_ERROR':
                            case 'MandatoryUnsatisfiedError':
                                HZRecorder.throwError('无法发现指定的硬件设备。');
                                break;
                            default:
                                HZRecorder.throwError('无法打开麦克风。异常信息:' + (error.code || error.name));
                                break;
                        }
                    });
            } else {
                HZRecorder.throwErr('当前浏览器不支持录音功能。'); return;
            }
        }
    }


    window.HZRecorder = HZRecorder;


})(window);
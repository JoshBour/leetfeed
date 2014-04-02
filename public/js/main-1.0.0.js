$(function () {

    var tag = document.createElement('script');
    var body = $('body');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    $('#mobileMenuToggle').on('click', function () {
        $("header nav").toggle();
    });

    // This will make all images with class hoverable to be hovered (duh)
    var previousImage = '';
    $('img[class*="hoverable"]').on('mouseenter',function () {
        var src = $(this).attr('src');
        previousImage = src;
        var details = getImageDetails(src);
        $(this).attr('src', baseUrl + '/images/' + details[0] + '-hover.' + details[1]);
    }).on('mouseleave', function () {
        $(this).attr('src', previousImage);
        previousImage = '';
    });

    $(document).on('submit', '#summonerForm', function () {
        var form = $(this);
        var parent = form.parent();
        parent.toggleLoadingImage();
        form.ajaxSubmit({
            target: '#summonerForm',
            success: function (responseText) {
                if (responseText.redirect) {
                    window.location.href = form.attr('action');
                }
                parent.toggleLoadingImage();
            },
            type: "post"
        });

        return false;
    });

    $(document).on('mouseenter mouseleave', '.verticalFeedList li', function () {
        var liItem = $(this);
        liItem.children('.feedMeta').slideToggle('fast');
    });

    $('.summonerMeta').on('click', function () {
        if (confirm("Do you really want to remove the summoner from your account?")) {
            var list = $(this).closest('li');
            $.ajax({
                url: baseUrl + '/account/remove-summoner',
                type: "POST",
                data: {
                    "summonerId": list.attr('class').substr(9)
                }
            }).success(function (data) {
                if (data.success == 1) {
                    var ul = list.parent();
                    if (ul.children().length == 1) {
                        ul.replaceWith($("<div/>", {
                            "class": "empty",
                            "text": "You haven't added a League of Legends account yet! Add one using the button on the right."
                        }));
                    }
                    list.detach();
                }
                addMessage(data.message);
            }).error(function () {
                addMessage('Something with wrong, please try again.');
            });
        }
    });

    $('#addSummonerToggle').on('click', function () {
        var form = $('#addSummoner');
        form.addClass('hideAfter').show().focusLight();
        focusedDiv = form;
    });

    $('#announcement .remove').on('click', function () {
        var elem = $(this).parent();
        var scope = elem.attr('data-scope');
        elem.detach();
        createCookie(scope, "true", 5);
    });

    $(".moduleSort select").on("change", function () {
        var option = $(this);
        window.location.href = option.val();
    });

    if ($('#main .module').attr('id') == "random") {
        $.ajax({
            url: baseUrl + '/feed/get-random-feed'
        }).success(function (data) {
            console.log(data);
            window.location.href = baseUrl + "/feed/" + data.feedId;
        }).error(function (jqXHR, textStatus) {
            console.log(textStatus);
            window.location.href = baseUrl + "/random";
        });
    }

    // open in new window
    $(document).on("click", '.openInWindow', function () {
        var url = $(this).attr("href");
        var windowName = "popUp";//$(this).attr("name");
        var windowSize = "width=650,height=400,scrollbars=yes";

        window.open(url, windowName, windowSize);

        event.preventDefault();
    });

    var focusedDiv = "";
    var flash = $('#flash');

    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    if (isMobile && body.hasClass('feedPage')) {
        $('#uploaded').after($('#share'));
    }

    if (!isMobile) {
        var players = [];
        var showDelay = 1000, setTimeoutConst;
        $(document).on('mouseenter', '.verticalFeedList li .feedThumbnail', function () {
            var feedThumbnail = $(this);
            setTimeoutConst = setTimeout(function () {
                var list = feedThumbnail.closest('li');
                var videoId = list.attr('data-video-id');
                var feedId = list.attr('class').substr(5);
                var thumbLink = feedThumbnail.children('a').find('img');
                thumbLink.hide();
                if (!players["preview-" + feedId]) {
                    players["preview-" + feedId] = new YT.Player("preview-" + feedId, {
                        height: '200',
                        width: '348',
                        videoId: videoId,
                        playerVars: { 'autoplay': 1, 'controls': 0, 'loop': 1, 'modestbranding': 0, 'showinfo': 0, 'autohide': 1},
                        events: {
                            'onReady': function (e) {
                                e.target.mute();
                            }
                        }
                    });
                } else {
                    var previewDiv = $("#preview-" + feedId);
                    if (!previewDiv.is("visible")) {
                        previewDiv.show();
                        players["preview-" + feedId].playVideo();
                    }
                }
            }, showDelay);
        });

        $(document).on('mouseleave', '.verticalFeedList li .feedThumbnail', function () {
            clearTimeout(setTimeoutConst);
            var feedThumbnail = $(this);
            var list = feedThumbnail.closest('li');
            var feedId = list.attr('class').substr(5);
            if (players["preview-" + feedId]) {
                var feedThumbnail = $(this);
                var list = feedThumbnail.closest('li');
                var feedId = list.attr('class').substr(5);
                var thumbLink = feedThumbnail.children('a').find('img');
                var player = players["preview-" + feedId];
                player.stopVideo();
                $("#preview-" + feedId).hide();
                thumbLink.show();
            }
        });
    }

    $('#commentList').perfectScrollbar({
        "wheelSpeed": 50
    });


    var addComment = $('#addComment');
    addComment.on('keyup', function () {
        var input = $(this);
        var val = input.val();
        var remainingChars = input.parent().find('#remainingChars .number');
        if (val.length > 150) {
            remainingChars.addClass("redFont");
        } else {
            remainingChars.removeClass("redFont");
            remainingChars.html(150 - val.length);
        }
    });

    addComment.pressEnter(function () {
        var input = $(this);
        var value = input.val();
        if (value.length <= 150) {
            var feedId = $('#feed').attr('class').substr(5);
            $.ajax({
                url: baseUrl + '/comment/add',
                type: "POST",
                data: {
                    "feedId": feedId,
                    "content": value
                },
                dataType: "html"
            }).success(function (data) {
                if (!isEmpty(data)) {
                    var emptyComDiv = $('.emptyComments');
                    if (emptyComDiv.length != 0) {
                        emptyComDiv.replaceWith($('<ul/>', {
                            "id": "commentList",
                            "html": data
                        }).perfectScrollbar({
                            "wheelSpeed": 50
                        }));
                    } else {
                        $('#commentList').append(data).perfectScrollbar("update");
                    }
                    input.val('');
                    input.parent().find('#remainingChars .number').html(150);
                }
            });
        }
    });

    if (body.hasClass('mainPage')) {
        var isScrolled = false;
        $(window).on("scroll", (function () {
            if (document.body.scrollHeight - $(this).scrollTop() <= $(this).height()) {
                if (!isScrolled) {
                    var list = $("#top-feeds");
                    var category = list.attr('id');
                    var page = parseInt(list.attr('data-page')) + 1;
                    var total = list.attr('data-total');
                    if (page - 1 >= total) {
                        var limitDiv = list.children('.limit-reached');
                        if (limitDiv.length == 0) {
                            limitDiv = $("<li />", {
                                "class": "limit-reached",
                                "text": "There are no more feeds available."
                            }).appendTo(list);
                        }
                    } else {
                        isScrolled = true;
                        list.toggleLoadingImage();
                        $.ajax({
                            url: baseUrl + '/get-more-feeds/' + category + '/' + page,
                            dataType: "html"
                        }).success(function (data) {
                            isScrolled = false;
                            list.append(data).perfectScrollbar("update").toggleLoadingImage();

                        });
                        list.attr('data-page', page);
                    }
                }
            }
        })
        );
    }

    $('.module ul.expandable').on("scroll", function (e) {
        var list = $(this);
        if (list.scrollTop() === list.prop('scrollHeight') - list.height()) {

        }
    });

    if (body.hasClass('famousPage')) {
        var isScrolled = false;
        $(window).on("scroll", (function () {
            if (document.body.scrollHeight - $(this).scrollTop() <= $(this).height()) {
                if (!isScrolled) {
                    isScrolled = true;
                    var list = $("#youtubersFeeds");
                    var nextPage = list.attr('data-token');
                    if (nextPage != "finished") {
                        var youtuberName = $('#youtubers li.active span').attr('class').substr(9);
                        list.toggleLoadingImage();
                        $.ajax({
                            url: baseUrl + '/feed/get-youtuber-feeds/' + youtuberName + '/false/' + nextPage,
                            timeout: 20000
                        }).success(function (data) {
                            var token = data.nextToken == null ? "finished" : data.nextToken;
                            list.attr('data-token', token);
                            // then do a request to get the feeds
                            $.ajax({
                                url: baseUrl + '/feed/get-youtuber-feeds/' + youtuberName + '/return/' + nextPage,
                                timeout: 20000
                            }).success(function (data) {
                                list.append(data);
                                list.toggleLoadingImage();
                                isScrolled = false;
                            }).error(function (jqXHR, textStatus) {
                                console.log(textStatus);
                            });
                        }).error(function (jqXHR, textStatus) {
                            console.log(textStatus);
                        });
                    }
                }
            }
        })
        );
    }

    var isSearchingYoutuberFeeds = false;
    $('#youtubers li').on("click", function () {
        var liItem = $(this);
        delay(function () {
            if (liItem.hasClass("active") || isSearchingYoutuberFeeds) return;
            isSearchingYoutuberFeeds = true;
            liItem.addClass("active");
            liItem.siblings(".active").removeClass("active");
            var feedsList = $('#youtubersFeeds');
            var module = feedsList.parent();
            var youtuberSpan = liItem.find('span[class^="youtuber"]');
            var youtuberName = youtuberSpan.attr('class').substring(9);
            feedsList.slideUp();
            module.toggleLoadingImage();

            // first do a request for the new youtuber's playlist token
            $.ajax({
                url: baseUrl + '/feed/get-youtuber-feeds/' + youtuberName,
                timeout: 20000
            }).success(function (data) {
                var token = data.nextToken == null ? "finished" : data.nextToken;
                feedsList.attr('data-token', token);
                // then do a request to get the feeds
                $.ajax({
                    url: baseUrl + '/feed/get-youtuber-feeds/' + youtuberName + '/return',
                    timeout: 20000
                }).success(function (data) {
                    feedsList.find('li').detach();
                    feedsList.prepend(data).slideDown();
                    feedsList.parent().siblings('h1').html(youtuberSpan.html() + "'s Latest Feeds");
                    module.toggleLoadingImage();
                    isSearchingYoutuberFeeds = false;
                    feedsList.perfectScrollbar("update");
                }).error(function (jqXHR, textStatus) {
                    console.log(textStatus);
                });
            }).error(function (jqXHR, textStatus) {
                console.log(textStatus);
            });
        }, 150);
    });

    $('.hideToggle').on('click', function () {
        var btn = $(this);
        var body = btn.siblings('.moduleBody');
        if (body.is(":visible")) {
            body.slideUp();
            btn.html("Show");
        } else {
            body.slideDown();
            btn.html("Hide");
        }
    });

    // if the shadow div exists, when you click outside of the
    // focused box it should be reverted to normal
    $(document).on('click', function (event) {
        if (event.target.id == 'shadow') {
            focusedDiv.unfocusLight();

            $('body').removeClass('unscrollable');
            if (focusedDiv.attr('id') == 'stage') {
                focusedDiv.find('iframe').detach();
                focusedDiv.hide();
            } else if (focusedDiv.hasClass('hideAfter')) {
                focusedDiv.hide();
            } else {
                focusedDiv.detach();
            }
        }
    });

    $(document).on('click', '.feedRating a', function (e) {
        var btn = $(this);
        if (btn.attr('href') == "#") {
            e.preventDefault()
        } else {
            return;
        }
        var otherBtn = btn.siblings('a[class^="thumb"]');
        if (!btn.hasClass('disabled')) {
            var rating = btn.attr("class");
            var feed = btn.parent().hasClass('embed') ? btn.closest('li') : $("#feed");
            var feedId = feed.attr('class').substring(5);
            btn.addClass('disabled');

            $.ajax({
                url: baseUrl + '/feed/rate/' + rating + '/id/' + feedId,
                timeout: 5000
            }).success(function (data) {
                if (data.success == 1) {
                    var totalSpan = feed.find('.count');
                    var total = parseInt(totalSpan.html());
                    if (otherBtn.hasClass("disabled")) {
                        otherBtn.removeClass('disabled');
                        total += (rating == "thumbUp") ? 2 : -2;
                    } else {
                        total += (rating == "thumbUp") ? 1 : -1;
                    }
                    totalSpan.html(total);
                } else {
                    addMessage(data.message);
                }
            });

        }
    });

    /**
     * @description Creates the stage and plays a video.
     */
    $(document).on('click', '.verticalFeedList li .videoMask', function (e) {
        if (!isMobile) {
            e.preventDefault();
            e.stopPropagation();
            var playBtn = $(this).parent();
            var listItem = playBtn.closest('li');
            var videoId = listItem.attr('data-video-id');
            var feedId = listItem.attr('class').substr(5);
            toggleVideo('hide');
            var stageWrapper = $('<div/>', {
                'id': "stageWrapper"
            }).prependTo($('body'));

            var stage = $('<div/>', {
                'id': 'stage'
            }).appendTo(stageWrapper);

            var videoWrapper = $('<div/>', {
                'id': 'videoWrapper'
            }).appendTo(stage);

            var videoPlayer = $('<iframe/>', {
                "id": "youtubeVideo",
                "frameborder": 0,
                "src": "http://www.youtube.com/embed/" + videoId + "?autoplay=1"
            }).appendTo(videoWrapper);

            focusedDiv = stageWrapper;
            stageWrapper.addClass('target-' + videoId).focusLight();
            $('body').addClass('unscrollable');
            $.ajax({
                url: baseUrl + '/feed/add-to-watched/' + feedId,
                timeout: 5000
            }).success(function (data) {
                if (data.success != 1) {
                    if (data.message != '') console.log(data.message);
                }
            });
        } else {
            var playLink = $(this).siblings('a');
            window.location.href = playLink.attr('href');
        }
    });

});

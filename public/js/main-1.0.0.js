$(function () {

    $('#mobileMenuToggle').on('click',function(e){
       $("header nav").toggle();
    });

    $(document).on('submit', '#summonerForm', function (e) {
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

    $('.summonerMeta').on('click', function (e) {
        if (confirm("Do you really want to remove the summoner from your account?")) {
            var list = $(this).closest('li');
            $.ajax({
                url: baseUrl + '/account/remove-summoner',
                type: "POST",
                data: {
                    "summonerId": list.attr('class').substr(9)
                }
            }).success(function (data) {
                if (data.success == 1){
                    var ul = list.parent();
                    if(ul.children().length == 1){
                        ul.replaceWith($("<div/>",{
                            "class" : "empty",
                            "text" : "You haven't added a League of Legends account yet! Add one using the button on the right."
                        }));
                    }
                    list.detach();
                }
                addMessage(data.message);
            }).error(function (jqXHR, textStatus) {
                addMessage('Something with wrong, please try again.');
            });
        }
    });

    $('#addSummonerToggle').on('click', function (e) {
        var form = $('#addSummoner');
        var clone = form.clone();
        form.addClass('hideAfter').show().focusLight();
        focusedDiv = form;
    });

    $('#announcement .remove').on('click', function (e) {
        var elem = $(this).parent();
        var scope = elem.attr('data-scope');
        elem.detach();
        createCookie(scope, "true", 5);
    });

    $(".moduleSort select").on("change", function (e) {
        var option = $(this);
        var url = option.val();
        window.location.href = url;
    });

    if ($('#main .module').attr('id') == "random") {
        $.ajax({
            url: baseUrl + '/feed/get-random-feed'
        }).success(function (data) {
            console.log(data);
            window.location.href = baseUrl + "/feed/" + data.feedId;
        }).error(function (jqXHR, textStatus) {
            console.log(status);
            window.location.href = baseUrl + "/random";
        });
    }

    // open in new window
    $(document).on("click", '.openInWindow', function (e) {
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

    $('.verticalFeedList').each(function (e) {
        $(this).perfectScrollbar({
            "wheelSpeed": 50
        });
    });

    $('#commentList').perfectScrollbar({
        "wheelSpeed": 50
    });

    $('#youtubers').perfectScrollbar({
        "wheelSpeed": 50,
        "suppressScrollY": true
    });

    $('#addComment').on('keyup', function (e) {
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

    $('#addComment').pressEnter(function () {
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
        } else {
            return;
        }
    });

    var isScrolled = false;
    $('.module ul.expandable').on("scroll", function (e) {
        var list = $(this);
        if (list.scrollTop() === list.prop('scrollHeight') - list.height()) {
            if (!isScrolled) {
                var category = list.attr('id');
                var page = parseInt(list.attr('data-page')) + 1;
                var total = list.attr('data-total');
                if (page - 1 >= total) {
                    var limitDiv = list.children('.limit-reached');
                    if (limitDiv.length != 0) {
                        return;
                    } else {
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
    });

    $('#youtubersFeeds').on("scroll", function (e) {
        var list = $(this);
        if (list.scrollTop() === list.prop('scrollHeight') - list.height()) {
            if (!isScrolled) {
                isScrolled = true;
                var nextPage = list.attr('data-token');
                if(nextPage != "finished"){
                    var youtuberName = $('#youtubers li.active span').attr('class').substr(9);
                    list.toggleLoadingImage();
                    $.ajax({
                        url: baseUrl + '/feed/get-youtuber-feeds/' + youtuberName + '/false/' + nextPage,
                        timeout: 20000
                    }).success(function (data) {
                        var token = data.nextToken == null ? "finished" : data.nextToken;
                        list.attr('data-token',token);
                        // then do a request to get the feeds
                        $.ajax({
                            url: baseUrl + '/feed/get-youtuber-feeds/' + youtuberName + '/return/' + nextPage,
                            timeout: 20000
                        }).success(function (data) {
                            list.append(data);
                            list.toggleLoadingImage();
                            isScrolled = false;
                            list.perfectScrollbar("destroy");
                            list.perfectScrollbar({
                                "wheelSpeed": 50
                            });
                        }).error(function (jqXHR, textStatus) {
                            console.log(status);
                        });
                    }).error(function (jqXHR, textStatus) {
                        console.log(status);
                    });
                }
            }
        }
    });

    var isSearchingYoutuberFeeds = false;
    $('#youtubers li').on("click", function (e) {
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
                feedsList.attr('data-token',token);
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
                    console.log(status);
                });
            }).error(function (jqXHR, textStatus) {
                console.log(status);
            });
        }, 150);
    });

    $('.hideToggle').on('click', function (e) {
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

    $(document).on('click', '#rating a', function (e) {
        var btn = $(this);
        if (btn.attr('href') == "#") {
            e.preventDefault()
        } else {
            return;
        }
        var otherBtn = btn.siblings('a[id^="thumb"]');
        if (!btn.hasClass('disabled')) {
            btn.addClass('disabled');
            var rating = btn.attr("id");
            var feed = $("#feed");
            var feedId = feed.attr('class').substring(5);

            $.ajax({
                url: baseUrl + '/feed/rate/' + rating + '/id/' + feedId,
                timeout: 5000
            }).success(function (data) {
                if (data.success == 1) {
                    var totalSpan = $("#total .count");
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
                    console.log(data.message);
                }
            });
        }
    });

});

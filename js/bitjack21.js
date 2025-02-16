var client_seed;
var server_seed_hash;
var tPos = 100;
var cardTime = 450;
var maxbet = null;
var minbet = null;
var prevHand = 0;
function genSeed() {
    var b = "0123456789abcdef";
    var c = 0;
    var a = "";
    var d;
    do {
        a = a + b.charAt(Math.floor(Math.random() * b.length));
        c++;
    } while (c < 32);
    return a;
}
function cardname(b) {
    if (b == 52) {
        return "bbb";
    }
    var a = "";
    var c = (b % 13) + 2;
    if (b <= 12) {
        a = "d";
    } else {
        if (b <= 25) {
            a = "h";
        } else {
            if (b <= 38) {
                a = "c";
            } else {
                if (b <= 51) {
                    a = "s";
                }
            }
        }
    }
    if (c < 10) {
        a += "0";
    }
    a += c;
    return a;
}
var deck = {
    baseDeck: [
        "d02",
        "d03",
        "d04",
        "d05",
        "d06",
        "d07",
        "d08",
        "d09",
        "d10",
        "d11",
        "d12",
        "d13",
        "d14",
        "h02",
        "h03",
        "h04",
        "h05",
        "h06",
        "h07",
        "h08",
        "h09",
        "h10",
        "h11",
        "h12",
        "h13",
        "h14",
        "c02",
        "c03",
        "c04",
        "c05",
        "c06",
        "c07",
        "c08",
        "c09",
        "c10",
        "c11",
        "c12",
        "c13",
        "c14",
        "s02",
        "s03",
        "s04",
        "s05",
        "s06",
        "s07",
        "s08",
        "s09",
        "s10",
        "s11",
        "s12",
        "s13",
        "s14",
        "bbb",
    ],
};
var dealer = { cardsShown: 0 };
var player1 = { cardsShown: 0 };
var player2 = { cardsShown: 0 };
var player3 = { cardsShown: 0 };
$(window).load(function () {
    $.post("control.php", { func: "getState", bet: 0 }, updateState, "json");
});
$(function () {
    imagePreload();
    $(".chip").click(function () {
        var d = parseInt($(this).attr("class").substr(6, 3));
        if (d == 0) {
            $("#bet").find("span").fadeOut("fast").html(d).fadeIn("fast");
        } else {
            var c = Number($("#bet").find("span").html()) + d;
            var mynum = Math.floor(Number($("#money").find("span").html()));
            var a = c;
            if (c > mynum || c > maxbet) {
                if (mynum > maxbet) {
                    a = maxbet;
                } else {
                    a = mynum;
                }
            }
            $("#bet").find("span").fadeOut("fast").html(a).fadeIn("fast");
        }
    });
    $("#deal").click(function () {
        $("#deal").hide();
        $(".chip").hide();
        client_seed = $("#csinput").val();
        if (!client_seed.match(/^[0-9a-f]*$/)) {
            alert("The client seed can only contain the characters 0-9 and a-f");
            $("#deal").show();
            $(".chip").show();
            return;
        }
        $("#csinput").hide();
        $("#cstext").html(client_seed).show();
        $(".cardM.dealer").remove();
        $(".cardM.player1").remove();
        $(".cardM.player2").remove();
        $(".cardM.player3").remove();
        $("#msg").hide();
        $("#p1msg").hide();
        $("#p2msg").hide();
        $("#p3msg").hide();
        $(".curValue").empty();
        player1.cardsShown = 0;
        player2.cardsShown = 0;
        player3.cardsShown = 0;
        dealer.cardsShown = 0;
        tPos = 100;
        var c = Number($("#bet").find("span").html());
        var mynum = Math.floor(Number($("#money").find("span").html()));
        var mynums = parseFloat(mynum).toFixed(2); //12.23
        var a = null;
        if (mynum < minbet) {
            a = "You don't have enough money. (Minimum bet is " + minbet + ".)";
        } else {
            if (c < minbet) {
                a = "The minimum bet is " + minbet + ".";
            }
        }
        if (a != null) {
            $("#msg").slideToggle("fast").html(a);
            setTimeout(function () {
                $("#msg").fadeOut("fast");
            }, 4500);
            $("#deal").show();
            $(".chip").show();
            $("#cpcurrenttext").html("Next Hand:");
            $("#cstext").hide();
            $("#csinput").show();
        } else {
            $.post("control.php", { func: "deal", bet: c, cseed: client_seed }, updateState, "json");
        }
    });
    $("#hit").click(function () {
        $("#deal").hide();
        $("#stay").hide();
        $("#hit").hide();
        $("#double").hide();
        $("#split").hide();
        $.post("control.php", { func: "hit", bet: 1 }, updateState, "json");
    });
    $("#stay").click(function () {
        $("#deal").hide();
        $("#stay").hide();
        $("#hit").hide();
        $("#double").hide();
        $("#split").hide();
        $.post("control.php", { func: "stay", bet: 1 }, updateState, "json");
    });
    $("#double").click(function () {
        $("#deal").hide();
        $("#stay").hide();
        $("#hit").hide();
        $("#double").hide();
        $("#split").hide();
        $.post("control.php", { func: "double", bet: 1 }, updateState, "json");
    });
    $("#split").click(function () {
        $("#deal").hide();
        $("#stay").hide();
        $("#hit").hide();
        $("#double").hide();
        $("#split").hide();
        $.post("control.php", { func: "split", bet: 1 }, updateState, "json");
    });
});
function showCards(c, d, b, f, e, g) {
    if (f == null) {
        f = cardTime;
    }
    if (e == null) {
        e = 0;
    }
    if (g == null) {
        g = 0;
    }
    var a = "";
    if (c == "dealer") {
        a = 20;
        xadjust = 0;
        yadjust = 0;
    } else {
        if (c == "player1") {
            a = 405 - (b - 1) * 20;
            xadjust = 0;
            yadjust = 0;
        } else {
            if (c == "player2") {
                a = 405 - (b - 1) * 20;
                xadjust = 240;
                yadjust = -30;
            } else {
                if (c == "player3") {
                    a = 405 - (b - 1) * 20;
                    xadjust = -200;
                    yadjust = 0;
                }
            }
        }
    }
    if (g == 0) {
        $("#gameField img")
            .eq(d)
            .clone()
            .appendTo("#gameField")
            .css("z-index", ++tPos)
            .addClass(c)
            .delay(e)
            .fadeIn(0)
            .animate({ top: a + yadjust, right: 385 - b * 20 + xadjust }, { queue: true, duration: f });
    } else {
        $(".cardM.player" + g)
            .eq(-1)
            .removeClass()
            .addClass("cardM")
            .addClass(c)
            .css("z-index", ++tPos)
            .delay(e)
            .fadeIn(0)
            .animate({ top: a + yadjust, right: 385 - b * 20 + xadjust }, { queue: true, duration: f });
    }
}
function imagePreload() {
    for (var a = 0; a < deck.baseDeck.length; a++) {
        $("#gameField").append('<img class="cardM" src="images/cards/' + deck.baseDeck[a] + '.png" alt="card"/>\n');
    }
}
function updateState(e) {
    var b = 0;
    var d = 1;
    if (e.errorCode == null || e.errorCode != 0) {
        var a = "Server Error";
        if (e.errorCode != null) {
            a = a + ": " + e.errorCode;
        }
        alert(a);
        return;
    }
    if (e.balance < $("#money").find("span").html() || $("#money").find("span").html() == "") {
        $("#money").find("span").fadeOut("fast").html(e.balance).fadeIn("fast");
        $("#bet").find("span").fadeOut("fast").html(e.bet).fadeIn("fast");
    }
    if (e.gameID != null) {
        $("#gameid").html("Game ID# " + e.gameID);
    }
    if (e.gameover != 1 && e.showDeal == 0 && e.thisR2 != null && e.thisR2.length > 0 && e.thisHR1RX != null && e.thisHR1RX.length > 0) {
        client_seed = e.thisR2;
        server_seed_hash = e.thisHR1RX;
        $("#cpcurrenttext").html("Current Hand");
        $("#csinput").hide();
        $("#cstext").html(client_seed).show();
        $("#cpcthr1rx").html(e.thisHR1RX);
    } else {
    }
    var f = null;
    var h = null;
    if (e.dcards != null && dealer.cardsShown == 0 && e.gameover == 1) {
        f = e.dcards[1];
        e.dcards[1] = e.dcards[0];
        e.dcards[0] = 52;
    }
    if (e.dcards != null && e.p1cards != null && dealer.cardsShown == 0 && player1.cardsShown == 0 && e.dcards.length == 2 && e.p1cards.length == 2 && e.p2cards == null && e.p3cards == null) {
        if (e.dcards.length >= 2 && e.p1cards.length >= 2) {
            showCards("player1", e.p1cards[0], 1);
            b++;
            player1.cardsShown++;
            showCards("dealer", e.dcards[0], 1, null, b * cardTime);
            b++;
            dealer.cardsShown++;
            showCards("player1", e.p1cards[1], 2, null, b * cardTime);
            b++;
            player1.cardsShown++;
            showCards("dealer", e.dcards[1], 2, null, b * cardTime);
            b++;
            dealer.cardsShown++;
            setTimeout(function () {
                $(".curValue.dealer").html(e.dscore);
            }, b * cardTime);
        }
    } else {
        if (e.dcards != null && dealer.cardsShown == 0) {
            h = cardTime;
            cardTime = 0;
            d = 0;
            for (var c = dealer.cardsShown; c < e.dcards.length; c++) {
                showCards("dealer", e.dcards[c], c + 1, null, b * cardTime);
                b++;
                dealer.cardsShown++;
            }
            setTimeout(function () {
                $(".curValue.dealer").html(e.p1score);
            }, b * cardTime);
        }
    }
    if (d == 1) {
        if (e.p3cards != null) {
            if (player3.cardsShown == 0 && player1.cardsShown > 0) {
                var g = 2;
                if (prevHand == 0) {
                    g = 1;
                }
                b++;
                showCards("player3", null, 1, null, (b - 1) * cardTime, g);
                player3.cardsShown++;
                if (g == 1) {
                    player1.cardsShown--;
                } else {
                    player2.cardsShown--;
                }
            }
        }
        if (e.p2cards != null) {
            if (player2.cardsShown == 0 && player1.cardsShown > 0) {
                b++;
                showCards("player2", null, 1, null, (b - 1) * cardTime, 1);
                player2.cardsShown++;
                player1.cardsShown--;
            }
        }
    }
    if (e.p1cards != null) {
        for (var c = player1.cardsShown; c < e.p1cards.length; c++) {
            showCards("player1", e.p1cards[c], c + 1, null, b * cardTime);
            b++;
            player1.cardsShown++;
        }
        setTimeout(function () {
            $(".curValue.player1").html(e.p1score);
        }, b * cardTime);
    }
    if (e.p2cards != null) {
        for (var c = player2.cardsShown; c < e.p2cards.length; c++) {
            showCards("player2", e.p2cards[c], c + 1, null, b * cardTime);
            b++;
            player2.cardsShown++;
        }
        setTimeout(function () {
            $(".curValue.player2").html(e.p2score);
        }, b * cardTime);
    }
    if (e.p3cards != null) {
        for (var c = player3.cardsShown; c < e.p3cards.length; c++) {
            showCards("player3", e.p3cards[c], c + 1, null, b * cardTime);
            b++;
            player3.cardsShown++;
        }
        setTimeout(function () {
            $(".curValue.player3").html(e.p3score);
        }, b * cardTime);
    }
    if (e.numSplits == 1 && e.gameover == 0) {
        if (e.currentHand == 0) {
            fadePlayer("player2", b * cardTime, 0);
            fadePlayer("player1", b * cardTime, 1);
        } else {
            fadePlayer("player1", b * cardTime, 0);
            fadePlayer("player2", b * cardTime, 1);
        }
    } else {
        if (e.numSplits == 2 && e.gameover == 0) {
            if (e.currentHand == 0) {
                fadePlayer("player2", b * cardTime, 0);
                fadePlayer("player3", b * cardTime, 0);
                fadePlayer("player1", b * cardTime, 1);
            } else {
                if (e.currentHand == 1) {
                    fadePlayer("player1", b * cardTime, 0);
                    fadePlayer("player3", b * cardTime, 0);
                    fadePlayer("player2", b * cardTime, 1);
                } else {
                    fadePlayer("player1", b * cardTime, 0);
                    fadePlayer("player2", b * cardTime, 0);
                    fadePlayer("player3", b * cardTime, 1);
                }
            }
        }
    }
    if (h != null) {
        cardTime = h;
        h = null;
        b = 0;
    }
    if (e.gameover == 1) {
        if (e.numSplits != 0) {
            fadePlayer("player1", b * cardTime, 1);
            fadePlayer("player2", b * cardTime, 1);
            fadePlayer("player3", b * cardTime, 1);
        }
        if (f != null) {
            e.dcards[0] = e.dcards[1];
            e.dcards[1] = f;
        }
        $(".curValue.dealer").html("");
        $(".cardM.dealer")
            .eq(0)
            .delay(b * cardTime)
            .animate(
                { top: 20, right: 325 },
                {
                    queue: true,
                    duration: cardTime,
                    complete: function () {
                        $(this).fadeOut(cardTime);
                        $("#gameField img")
                            .eq(e.dcards[1])
                            .clone()
                            .appendTo("#gameField")
                            .css("z-index", ++tPos)
                            .addClass("dealer")
                            .animate({ top: 20, right: 325 }, { queue: false, duration: 0 })
                            .css("z-index", ++tPos)
                            .fadeIn(cardTime);
                        var k = 1;
                        for (var l = dealer.cardsShown; l < e.dcards.length; l++) {
                            b++;
                            showCards("dealer", e.dcards[l], l + 2, cardTime, k * cardTime);
                            dealer.cardsShown++;
                            k++;
                        }
                        setTimeout(function () {
                            $(".curValue.dealer").html(e.dscore);
                            updateState2(e);
                            $.post("control.php", { func: "getState", bet: 1 }, updateState, "json");
                        }, k * cardTime);
                    },
                }
            );
    } else {
        setTimeout(function () {
            updateState2(e);
        }, b * cardTime);
    }
}
function updateState2(c) {
    if (c.balance > $("#money").find("span").html()) {
        $("#money").find("span").fadeOut("fast").html(c.balance).fadeIn("fast");
    }
    maxbet = c.maxbet;
    minbet = c.minbet;
    if (c.gameover == 1) {
        $("#cpltgid").html(c.gameID);
        $("#cpltr1").html(c.thisR1);
        $("#cpltrx").html(c.thisRX);
        $("#cplthr1rx").html(c.thisHR1RX);
        $("#cpltr2").html(c.thisR2);
        var b = Number(c.bet);
        var mynum = Math.floor(Number($("#money").find("span").html()));
	var mynums = parseFloat(mynum).toFixed(2); //12.23
        if (b > a || b > maxbet) {
            if (a > maxbet) {
                b = maxbet;
            } else {
                b = a;
            }
            $("#bet").find("span").fadeOut("fast").html(b).fadeIn("fast");
        }
    }
    if (c.msg.length > 0) {
        $("#msg").slideToggle("fast").html(c.msg);
    }
    if (c.p1msg.length > 0) {
        $("#p1msg").slideToggle("fast").html(c.p1msg);
    }
    if (c.p2msg.length > 0) {
        $("#p2msg").slideToggle("fast").html(c.p2msg);
    }
    if (c.p3msg.length > 0) {
        $("#p3msg").slideToggle("fast").html(c.p3msg);
    }
    if (c.showDeal == 1) {
        client_seed = genSeed();
        server_seed_hash = c.nextHR1RX;
        $("#cpcurrenttext").html("Next Hand:");
        $("#cstext").hide();
        $("#csinput").val(client_seed).show();
        $("#cpcthr1rx").html(c.nextHR1RX);
        if (c.showHit != 0 || c.showStay != 0 || c.showDouble != 0 || c.showSplit != 0) {
            alert("Server Display Error 1");
            return;
        }
        $("#deal").css("margin-left", "144px").show();
        $(".chip").show();
    } else {
        if (c.showHit == 1) {
            if (c.showStay != 1) {
                alert("Server Display Error 2");
                return;
            }
            if (c.showDouble == 1) {
                $("#double").css("margin-left", "21px").show();
                $("#hit").css("margin-left", "21px").show();
            } else {
                $("#hit").css("margin-left", "103px").show();
            }
            $("#stay").css("margin-left", "21px").show();
            if (c.showSplit == 1) {
                $("#split").css("margin-left", "21px").show();
            }
        }
    }
    prevHand = c.currentHand;
}
function fadePlayer(b, a, c) {
    if (c == 0) {
        c = 0.2;
    }
    $(".cardM." + b).css("opacity", c);
}


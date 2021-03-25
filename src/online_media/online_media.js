var online_media_opacity = document.getElementsByClassName("online_media_opacity")[0],
	online_media = document.getElementsByClassName("online_media")[0],
	online_media_toggle = online_media.getElementsByClassName("toggle")[0],
	online_media_loaded = false,

	lofi_toggled = false;

Event.add(online_media_toggle, "click", () => {
	online_media.classList.toggle("shown");
	load_online_media();
});

Event.add(window, "mousedown", (e) => {
	if (online_media.classList.contains("shown") && !online_media.contains(e.target)) {
		online_media.classList.remove("shown");
	}
});

Event.add(html, "interacted", (e) => {
	let url = new URL(window.location.href);
	if (url.searchParams.has("lofi") && url.searchParams.get("lofi") == "1") {
		url.searchParams.delete("lofi");
		set_url(url.href);
		load_online_media();
	}
});


// =================================== YouTube ===================================

function load_online_media(argument) {
	if (online_media_loaded) return;

	var tag = document.createElement('script');
	tag.src = "https://www.youtube.com/iframe_api";
	document.body.append(tag);
	online_media_loaded = true;
}

var player;
function onYouTubePlayerAPIReady() {
	// console.log("onYouTubePlayerAPIReady");

	player = new YT.Player("ytplayer", {
		videoId: "5qap5aO4i9A",

		playerVars: {
			autoplay: 0,
			cc_load_policy: 0,
			controls: 1,
			fs: 0,
			hl: "ru",
			iv_load_policy: 3,
			modestbranding: 1,
			rel: 0,
			showinfo: 0,
		},

		events: {
			'onReady': onPlayerReady,
			'onPlaybackQualityChange': onPlayerPlaybackQualityChange,
			'onStateChange': onPlayerStateChange,
			'onError': onPlayerError
		}
	});
}

function onPlayerReady(event) {
	event.target.playVideo();
	lofi_toggled = true;
}

function onPlayerPlaybackQualityChange(event) {
	// SMTH
}

function onPlayerStateChange(event) {
	if (event.data == 1) {
		for (let menu_link of menu_links) {
			let url = new URL(menu_link.href);
			url.searchParams.set("lofi", "1");
			menu_link.href = url.href;
		}

	} else if (event.data == 2) {
		for (let menu_link of menu_links) {
			let url = new URL(menu_link.href);
			url.searchParams.delete("lofi");
			menu_link.href = url.href;
		}
	}
}

function onPlayerError(event) {
	console.log("YT player error");
	console.log(event);
}

# HLS Player

A video player that can play any video file in the browser using ffmpeg as backend.

# Introduction

I was impressed with how plex, jellyfin, emby and other media players were able to play any video file in the browser,
and transcode the media on the fly, I wanted something simpler that I can integrate into my server management app, and I
managed to make it work with the majority of the media files that I came cross, and I thought that the player might be
useful for somebody else.

# Container Limitation

Sadly, I was not able to get a good alpine build with working hardware acceleration, so right now in the container build
only software encoding works. However, the video player support hardware accel, if there is interest I may build
different container that has hardware acceleration support.

# How does the player works?

When you select a video file, you will be presented with a screen that give you options on how to encode the video file,
after you finish selecting what you want, and click **Stream** it will hand off the file to a controller that would
segment the video file into chunks to support seeking, we use HLS protocol to support wide devices, we tested on
iOS/chrome/firefox, and the defaults works. 

## Install

create your `docker-compose.yaml` file

```yaml
version: '3.3'
services:
    hls_player:
        image: arabcoders/hls_player:latest
        container_name: hls_player
        restart: unless-stopped
        environment:
            VP_UID: ${UID:-1000} # Set container operation user id.
            VP_GID: ${GID:-1000} # Set container operation group id.
        ports:
            - "8081:80" # HTTP server port
        volumes:
            - ${PWD}:/config:rw # mount current directory to container /config directory.
            - /mnt/media:/storage:ro # mount your media files.
```

After creating your docker-compose file, start the container.

```bash
$ docker-compose up -d
```

Now, go to `http://localhost:8081/` and browse your media.

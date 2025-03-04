#########################################################################################
#
#
# Java for Jenkins  
#
# ource https://github.com/jenkinsci/docker-agent/blob/master/alpine/Dockerfile
#
##########################################################################################
# 

ARG ALPINE_TAG=3.21.3
FROM alpine:"${ALPINE_TAG}" AS jre-build

SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

# This Build ARG is populated by Docker
# Ref. https://docs.docker.com/engine/reference/builder/#automatic-platform-args-in-the-global-scope
ARG TARGETPLATFORM

COPY adoptium-get-jdk-link.sh /usr/bin/local/adoptium-get-jdk-link.sh
COPY adoptium-install-jdk.sh /usr/bin/local/adoptium-install-jdk.sh

ARG JAVA_VERSION=17.0.14_7
# hadolint ignore=DL3018
RUN apk add --no-cache \
    ca-certificates \
    jq \
    curl \
  && /usr/bin/local/adoptium-install-jdk.sh alpine

ENV PATH="/opt/jdk-${JAVA_VERSION}/bin:${PATH}"

RUN case "$(jlink --version 2>&1)" in \
      "17."*) set -- "--compress=2" ;; \
      # the compression argument is different for JDK21
      "21."*) set -- "--compress=zip-6" ;; \
      *) echo "ERROR: unmanaged jlink version pattern" && exit 1 ;; \
    esac; \
    jlink \
      --strip-java-debug-attributes \
      "$1" \
      --add-modules ALL-MODULE-PATH \
      --no-man-pages \
      --no-header-files \
      --output /javaruntime


#########################################################################################
#
#
# Php for the application  
#
#
##########################################################################################
FROM php:8.2-fpm
RUN useradd -rm -d /home/jenkins -s /bin/bash -g root -G sudo -u 1001 jenkins
SHELL ["/bin/bash", "-o", "pipefail", "-c"]
RUN echo 'jenkins:jenkins' | chpasswd
USER jenkins
WORKDIR /home/jenkins



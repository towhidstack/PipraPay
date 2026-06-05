#!/bin/bash
# Bootstrap logging — quiet by default; set PIPRAPAY_BOOTSTRAP_VERBOSE=1 for full startup lines.

piprapay_verbose() {
    [ "${PIPRAPAY_BOOTSTRAP_VERBOSE:-0}" = "1" ]
}

piprapay_log() {
    if piprapay_verbose; then
        echo "$@"
    fi
}

piprapay_warn() {
    echo "$@" >&2
}

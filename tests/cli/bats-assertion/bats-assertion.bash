#!/usr/bin/env bash

# Bats Assertion
# URL: https://github.com/thingsym/bats-assertion
# Version: 0.1.0
# Author: thingsym
# distributed under MIT.
# Copyright (c) 2018 thingsym

assert_success() {
    if [ "${status}" -ne 0 ]; then
        echo "Expected: 0"
        echo "Actual  : ${status}"
        return 1
    fi
}

assert_failure() {
    if [ "${status}" -eq 0 ]; then
        echo "Expected: non-zero exit code"
        echo "Actual  : ${status}"
        return 1
    fi
}

assert_status() {
    if [ "${status}" -ne "${1}" ]; then
        echo "Expected: ${1}"
        echo "Actual  : ${status}"
        return 1
    fi
}

assert_equal() {
    _get_actual_output "${2}"

    if [ "${1}" != "${actual_output}" ]; then
        echo "Expected: ${1}"
        echo "Actual  : ${actual_output}"
        return 1
    fi
}

assert_fail_equal() {
    _get_actual_output "${2}"

    if [ "${1}" = "${actual_output}" ]; then
        echo "Unexpected: ${1}"
        echo "Actual    : ${actual_output}"
        return 1
    fi
}

assert_match() {
    _get_actual_output "${2}"

    if [[ ! "${actual_output}" =~ ${1} ]]; then
        echo "Expected: ${1}"
        echo "Actual  : ${actual_output}"
        return 1
    fi
}

assert_fail_match() {
    _get_actual_output "${2}"

    if [[ "${actual_output}" =~ ${1} ]]; then
        echo "Unexpected: ${1}"
        echo "Actual    : ${actual_output}"
        return 1
    fi
}

assert_lines_equal() {
    _get_actual_line_output "${2}"

    if [ -z "${2}" ]; then
        local match=0
        for actual_line in ${lines[@]}; do
            if [[ "${actual_line}" = "${1}" ]]; then
                match=1
                break
            fi
        done
        if [ "$match" = 0 ]; then
            echo "Expected: ${1}"
            echo "Actual  : ${output}"
            return 1
        fi
    elif [ ! "${actual_output}" = "${1}" ]; then
        echo "Expected: ${1}"
        echo "Actual  : ${actual_output}"
        echo "Index   : ${actual_index}"
        return 1
    fi
}

assert_fail_lines_equal() {
    _get_actual_line_output "${2}"

    if [ -z "${2}" ]; then
        local match=0
        for actual_line in ${lines[@]}; do
            if [[ "${actual_line}" = "${1}" ]]; then
                match=1
                break
            fi
        done
        if [ "$match" = 1 ]; then
            echo "Unexpected: ${1}"
            echo "Actual    : ${output}"
            return 1
        fi
    elif [ "${actual_output}" = "${1}" ]; then
        echo "Unexpected: ${1}"
        echo "Actual    : ${actual_output}"
        echo "Index     : ${actual_index}"
        return 1
    fi
}

assert_lines_match() {
    _get_actual_line_output "${2}"

    if [ -z "${2}" ]; then
        local match=0
        for actual_line in ${lines[@]}; do
            if [[ "${actual_line}" =~ ${1} ]]; then
                match=1
                break
            fi
        done
        if [ "$match" = 0 ]; then
            echo "Expected: ${1}"
            echo "Actual  : ${output}"
            return 1
        fi
    elif [[ ! "${actual_output}" =~ ${1} ]]; then
        echo "Expected: ${1}"
        echo "Actual  : ${actual_output}"
        echo "Index   : ${actual_index}"
        return 1
    fi
}

assert_fail_lines_match() {
    _get_actual_line_output "${2}"

    if [ -z "${2}" ]; then
        local match=0
        for actual_line in ${lines[@]}; do
            if [[ "${actual_line}" =~ ${1} ]]; then
                match=1
                break
            fi
        done
        if [ "$match" = 1 ]; then
            echo "Unexpected: ${1}"
            echo "Actual    : ${output}"
            return 1
        fi
    elif [[ "${actual_output}" =~ ${1} ]]; then
        echo "Unexpected: ${1}"
        echo "Actual    : ${actual_output}"
        echo "Index     : ${actual_index}"
        return 1
    fi
}

_get_actual_output() {
    if [ -z "${1}" ]; then
        actual_output="${output}"
    else
        actual_output="${1}"
    fi
}

_get_actual_line_output() {
    if [ ! -z "${1}" ]; then
        if [ "${1}" = "first" ]; then
            actual_index=0
        elif [ "${1}" = "last" ]; then
            actual_index="$(expr ${#lines[@]} - 1)"
        else
            actual_index="${1}"
        fi

        actual_output="${lines[${actual_index}]}"
    fi
}

_dump() {
    echo "---- Dumper START ----"
    if [ -z "${1}" ]; then
        echo "${output}"
        echo "status : ${status}"
    else
        echo "${1}"
    fi
    echo "---- Dumper END ----"

    return 1
}

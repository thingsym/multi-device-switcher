#!/usr/bin/env bats

load bats-assertion/bats-assertion

setup() {
  wp multi-device reset
  wp multi-device theme smartphone 'None'
  wp multi-device theme tablet 'None'
  wp multi-device theme mobile 'None'
  wp multi-device theme game 'None'

  run wp multi-device delete testtest
}

@test "get status" {
  run wp multi-device status

  assert_success
  assert_status 0
}

@test "get theme" {
  run wp multi-device theme smartphone

  assert_success
  assert_status 0
  assert_equal "Success: None | "

  run wp multi-device theme smartphone twentysixteen

  assert_success
  assert_status 0
  assert_equal "Success: switch smartphone theme to Twenty Sixteen"

  run wp multi-device theme smartphone

  assert_success
  assert_status 0
  assert_equal "Success: Twenty Sixteen | twentysixteen"
}

@test "switch theme" {
  run wp multi-device theme smartphone twentyfifteen

  assert_success
  assert_status 0
  assert_equal "Success: switch smartphone theme to Twenty Fifteen"

  run wp multi-device theme smartphone

  assert_success
  assert_status 0
  assert_equal "Success: Twenty Fifteen | twentyfifteen"

  run wp multi-device theme smartphone --theme='Twenty Sixteen'

  assert_success
  assert_status 0
  assert_equal "Success: switch smartphone theme to Twenty Sixteen"

  run wp multi-device theme smartphone

  assert_success
  assert_status 0
  assert_equal "Success: Twenty Sixteen | twentysixteen"
}

@test "get UserAgent" {
  run wp multi-device useragent tablet

  assert_success
  assert_status 0
  assert_equal "Success: iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko"
}

@test "set UserAgent" {
  run wp multi-device useragent tablet 'iPad, Kindle, Sony Tablet, Nexus 7'

  assert_success
  assert_status 0
  assert_equal "Success: set tablet UserAgent to iPad, Kindle, Sony Tablet, Nexus 7"
}

@test "reset UserAgent" {
  run wp multi-device useragent smart 'aaa'
  run wp multi-device useragent tablet 'bbb'
  run wp multi-device useragent mobile 'ccc'
  run wp multi-device useragent game 'ddd'

  run wp multi-device reset

  assert_success
  assert_status 0
  assert_equal "Success: reset Settings to Default UserAgent"

  run wp multi-device status

  assert_success
  assert_status 0

  assert_lines_equal "smartphone (Smart Phone)	None		iPhone, iPod, Android.*Mobile, dream, CUPCAKE, Windows Phone, IEMobile.*Touch, webOS, BB10.*Mobile, BlackBerry.*Mobile, Mobile.*Gecko" 2
  assert_lines_equal "tablet (Tablet PC)	None		iPad, Kindle, Silk, Android(?!.*Mobile), Windows.*Touch, PlayBook, Tablet.*Gecko" 3
  assert_lines_equal "mobile (Mobile Phone)	None		DoCoMo, SoftBank, J-PHONE, Vodafone, KDDI, UP.Browser, WILLCOM, emobile, DDIPOCKET, Windows CE, BlackBerry, Symbian, PalmOS, Huawei, IAC, Nokia" 4
  assert_lines_equal "game (Game Platforms)	None		PSP, PS2, PLAYSTATION 3, PlayStation (Portable|Vita|4|5), Nitro, Nintendo (3DS|Wii|WiiU|Switch), Xbox" 5
}

@test "add Custom Switcher" {
  run wp multi-device add testtest

  assert_success
  assert_status 0
  assert_equal "Success: add testtest Custom Switcher"

  run wp multi-device delete testtest
  run wp multi-device add testtest twentyfifteen "iPad, Kindle, Sony Tablet, Nexus 7"

  assert_success
  assert_status 0
  assert_equal "Success: add testtest Custom Switcher"

  run wp multi-device theme testtest

  assert_success
  assert_status 0
  assert_equal "Success: Twenty Fifteen | twentyfifteen"

  run wp multi-device useragent testtest

  assert_success
  assert_status 0
  assert_equal "Success: iPad, Kindle, Sony Tablet, Nexus 7"

  run wp multi-device delete testtest
  run wp multi-device add testtest --theme='Twenty Fifteen'

  assert_success
  assert_status 0
  assert_equal "Success: add testtest Custom Switcher"

  run wp multi-device theme testtest

  assert_success
  assert_status 0
  assert_equal "Success: Twenty Fifteen | twentyfifteen"
}

@test "delete Custom Switcher" {
  run wp multi-device add testtest twentyfifteen "iPad, Kindle, Sony Tablet, Nexus 7"
  run wp multi-device delete testtest

  assert_success
  assert_status 0
  assert_equal "Success: delete testtest Custom Switcher"
}

@test "turn on/off PC Switcher" {
  run wp multi-device pc-switcher on

  assert_success
  assert_status 0
  assert_equal "Success: turn on PC Switcher"

  run wp multi-device pc-switcher off

  assert_success
  assert_status 0
  assert_equal "Success: turn off PC Switcher"
}

@test "turn on/off default CSS" {
  run wp multi-device css on

  assert_success
  assert_status 0
  assert_equal "Success: turn on default CSS"

  run wp multi-device css off

  assert_success
  assert_status 0
  assert_equal "Success: turn off default CSS"
}

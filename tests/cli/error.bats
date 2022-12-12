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

@test "switch theme - fail" {
  wp theme activate twentynineteen

  run wp multi-device theme smartphone twentynineteen

  assert_failure
  assert_status 1
  assert_equal "Error: Twenty Nineteen theme is in active"

  run wp multi-device theme smartphone --theme='Twenty Nineteen'

  assert_failure
  assert_status 1
  assert_equal "Error: Twenty Nineteen theme is in active"

  run wp multi-device theme smartphone aaaa

  assert_failure
  assert_status 1
  assert_equal "Error: aaaa theme is not installed"

  run wp multi-device theme smartphone --theme='aaaa'

  assert_failure
  assert_status 1
  assert_equal "Error: aaaa theme is not installed"

  run wp multi-device theme testtest

  assert_failure
  assert_status 1
  assert_equal "Error: testtest don't exist"

  run wp multi-device theme testtest aaaa

  assert_failure
  assert_status 1
  assert_equal "Error: aaaa theme is not installed"
}

@test "set UserAgent - fail" {
  run wp multi-device useragent testtest 'iPad, Kindle, Sony Tablet, Nexus 7'

  assert_failure
  assert_status 1
  assert_equal "Error: testtest don't exist"

  run wp multi-device useragent testtest

  assert_failure
  assert_status 1
  assert_equal "Error: testtest don't exist"
}

@test "add Custom Switcher - fail" {
  run wp multi-device add testtesttesttesttesttest

  assert_failure
  assert_status 1
  assert_equal "Error: 20 characters max, alphanumeric"

  run wp multi-device add smart

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't add"

  run wp multi-device add tablet

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't add"

  run wp multi-device add mobile

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't add"

  run wp multi-device add game

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't add"

  run wp multi-device add testtest
  run wp multi-device add testtest

  assert_failure
  assert_status 1
  assert_equal "Error: Custom Switcher already exists"

  wp theme activate twentynineteen

  run wp multi-device delete testtest
  run wp multi-device add testtest twentynineteen "iPad, Kindle, Sony Tablet, Nexus 7"

  assert_failure
  assert_status 1
  assert_equal "Error: Twenty Nineteen theme is in active"

  run wp multi-device delete testtest
  run wp multi-device add testtest aaaa

  assert_failure
  assert_status 1
  assert_equal "Error: aaaa theme is not installed"
}

@test "delete Custom Switcher - fail" {
  run wp multi-device delete smartphone

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't delete"

  run wp multi-device delete smart

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't delete"

  run wp multi-device delete tablet

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't delete"

  run wp multi-device delete mobile

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't delete"

  run wp multi-device delete game

  assert_failure
  assert_status 1
  assert_equal "Error: Default Switcher can't delete"

  run wp multi-device delete testtest

  assert_failure
  assert_status 1
  assert_equal "Error: Custom Switcher don't exist"
}

@test "turn on/off PC Switcher - fail" {
  run wp multi-device pc-switcher xxx

  assert_failure
  assert_status 1
  assert_equal "Error: Invalid flag"
}

@test "turn on/off default CSS - fail" {
  run wp multi-device css xxx

  assert_failure
  assert_status 1
  assert_equal "Error: Invalid flag"
}

---
title: TODO

access:
    admin.comments: true
    admin.super: true

content:
    items: '@root.descendants'
    order:
        by: header.todo.priority
        dir: desc
    # TODO Pagination is currently not available for admin plugin:
    # https://github.com/getgrav/grav-plugin-admin/issues/1994
    #limit: 10
    pagination: true
---

# Security

Security in BearIT is implemented on top of **Roles**, **Policies**, **Access Functions** and **Limitations**.

**Access functions** are the hearth of security. Tells what user can do. For example if user can remove project that means
the same as *user has a function to remove a project*.

```
$userAccess->isGranted(AccessFunction::fromString('project/remove'), $project);
```

But wait! We don't want to allow user remove any project! He can delete only project he owns. Here come **Limitations**.
Limitation tells if user actually can do something with the subject we are checking against.

To better illustrate this let's first explain Roles and Policies. Role is simple a set of policies. Policy provides
functions. Policies can be also limited. Policy can be defined by multiple limitations and all must be fulfilled.
In other words limitation conditions are checked with AND logical operator.

When we want user to be able to have some functions for limitation A or limitation B then we can achieve this
by defining two separate Policies.

## AccessMap

When we have defined roles we can build an AccessMap. It basically calculates all functions what are provided
by the roles and on what conditions (limitations).

```
$accessMap = new BearIt\Access\Model\User\AccessMap($roles);
```

Now we can check if access map grants access to specified function:

```
$accessMap->grantsAccess(
    /* AccessFunction */ $function,
    /* UserId */ $userId,
    /* mixed */ $subject
)
```

Also we can use a `BearIt\Access\Model\User\User` class what is recommended as the accessMap will be lazily built when
we first time will check if user is granted for some function. It also adds helper functions `allGranted` and
`anyGranted` to check if user has respectively all or at least one access function.  

```
$userAccess = new BearIt\Access\Model\User\User($userId, $roles)

$userAccess->isGranted(
    /* AccessFunction */ $function,
    /* mixed */ $subject
)

$userAccess->anyGranted(
    /* AccessFunction[] */ $functions,
    /* mixed */ $subject
)

$userAccess->allGranted(
    /* AccessFunction[] */ $functions,
    /* mixed */ $subject
)
```

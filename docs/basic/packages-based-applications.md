# On packages based applications

PHP like any language has known _a lot_ of frameworks and microframeworks, of all kinds and with different capabilities. Some stood the test of time, some didn't.

Since a couple of years though, a trend has established itself among them: frameworks aren't monolithic black boxes anymore, they're an ensemble of small components.
And if the framework is well done, these components can be used easily outside of the framework, by themselves.

In parallel, thanks to the growth of [Composer] and the efforts of the [PHP-FIG], people have started to release more and more agnostic packages, standing on common interfaces shared and used between frameworks and microframeworks.
And if no interface is available, a lot of package maintainers nowadays ship serveral service providers for all your needs.

My point is, there isn't _necessarily_ a need for microframeworks anymore; instead of being constrained and locked to a certain architecture and to certain packages, you can now just
smash a bunch of packages together to make an app... and that is wonderful.

It's the same trend you can observe in Javascript where a lot of people decided to abandon the Angular or Ember ship to instead leverage the vast NPM ecosystem and create their own patchwork, with only what they need and/or want.

## In which case are packages-based applications a plus?

If you don't want to go the framework route but want to avoid reinventing the wheel at all costs. Or if you like your current framework but sometimes wish you could just swap its router for X or its logger for Y, etc

Packages based applications give you the liberty to pick whatever components and structure you **like** instead of whatever the framework maintainers decided was best.
This doesn't mean you're abandonning all their work, quite the contrary. The Composer ecosystem has nowadays matured far enough for this to be possible without abandonning the
wonderful work made by the contributors of Symfony, Laravel, etc.

You like Laravel's Eloquent? Pull in `illuminate/database`. You like Symfony's Console? Pull in `symfony/console`. None of their router fits your need?
[Pick any of the 500 routers on Packagist](https://packagist.org/search/?q=router&orderBys%5B0%5D%5Bsort%5D=downloads&orderBys%5B0%5D%5Border%5D=desc).

 Now smash all of them together, and you have an app. Simple as that.

## So what's the catch?

The catch is that since we're talking about an ensemble of components that were made to be as decoupled as possible, bringing them together under one roof requires a bit of boilerplate.
Even more if the components you're binding come from very different ecosystems – there is a world between an Illuminate package and an Aura package.

That's where **Glue** comes in, it's here to do the boilerplate for you and let you focus on what's important: picking the packages and structure you like, and doing your thing.

[composer]: https://getcomposer.org
[php-fig]: http://www.php-fig.org

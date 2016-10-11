<?php

namespace Avram\Translatable\Middlewares;

use Avram\Translatable\Translatable;
use Closure;

class HideDefaultLanguageCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Translatable $instance */
        $instance = app(Translatable::class);

        if ($instance->hasValidLanguageSegment()
            && $instance->getLanguage()->isDefault()
            && $instance->hideDefault) {

            $path = $instance->preparePath($request->path(), $instance->getDefaultLanguage()->getCode());
            return redirect($instance->url($path, [], $instance->getDefaultLanguage()->getCode()));
        }
        return $next($request);
    }
}

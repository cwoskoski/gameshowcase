@extends('layouts.layout')

<?php
$types = \App\Submission::getTypes();
?>

@section('title', $submission->title . " - GGMaker Downloads")

@section('content')

<div class="submission-controls pull-right">
    @if (Auth::user() && Auth::user()->access == 'admin')
        @if ($submission->approved != 1)
            <a class="btn btn-default" href="{{ URL::action('AdminController@approve', $submission->id) }}">Approve</a>
            <a class="btn btn-default deny-submission" href="{{ URL::action('AdminController@deny', $submission->id) }}">Deny</a>
        @else
            <a class="btn btn-default" href="{{ URL::action('AdminController@suspend', $submission->id) }}">Suspend</a>
        @endif
    @endif
    @if (Auth::user() && $submission->user->id == Auth::user()->id)
    <a class="btn btn-default" href="{{ URL::action('SubmissionController@edit', $submission->id) }}">Edit</a>
    @endif
    @if ($submission->isPlayable())
    <a href="{{ route('submission.play', $submission->id) }}" class="btn btn-primary" title="Play {{ $submission->title }}"><i class="fa fa-gamepad"></i> Play</a>
    @endif
    <a href="{{ route('submission.rate', $submission->id) }}" class="btn btn-success rate-submission" title="Rate This!"><i class="fa fa-thumbs-up"></i> <span class="badge">{{ count($submission->ratings) }}</span></a>
</div>

<h2>{{ $submission->title }}</h2>
<div class="row">
    <div class="col-md-4">
        <div class="well">
            <dl style="margin-bottom: 0;">
                <dt>Author</dt>
                <dd>{{ $submission->user->name }}</dd>
                @if ($submission->type != 'tutorial')
                <dt>Category</dt>
                <dd>{{ $submission->category() }}</dd>
                @endif
                @if ($submission->getMeta('status'))
                <dt>Status</dt>
                <dd>{{ $submission->getMeta('status') }}</dd>
                @endif
                @if ($submission->getMeta('version'))
                <dt>Version</dt>
                <dd>{{ $submission->getMeta('version') }}</dd>
                @endif
                <dt>Languages</dt>
                <dd>{!! implode(', ', $submission->languages()) !!}</dd>
                <dt>Release Date</dt>
                <dd>{{ $submission->created_at->format('Y-m-d') }}</dd>
                <dt>Last Update</dt>
                <dd>{{ $submission->updated_at->format('Y-m-d') }}</dd>
            </dl>
        </div>
        @if (count($submission->contents()) > 0)
        <div class="panel panel-warning">
            <div class="panel-heading"><i class="fa fa-warning"></i> Content Ratings</div>
            <ul class="list-group">
                @foreach($submission->contents() as $c)
                    <li class="list-group-item">
                        {{ $c}}
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (count($files) > 0)
            <div class="panel panel-default">
                <div class="panel-heading">Files</div>
                <div class="list-group">
                    @foreach($files as $file)
                        {!! Html::link('/submissions/'.$submission->id.'/media/'.$file->id.'/media', 'Download ' . $file->file_path, ['target' => '_blank', 'class' => 'list-group-item', 'style' => 'word-wrap: break-word']) !!}
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $submission->type == 'tutorial' ? 'Tutorial' : 'Description' }}</h3>
            </div>
            <div class="panel-body">
                <div class="pager-container">
                    <?php
                    $paginationEnabled = false;
                    $description = $submission->description;
                    $description = str_replace("<p><!-- pagebreak --></p>", "<!-- pagebreak -->", $description);
                    if (stristr($description, '<!-- pagebreak -->')) {
                        $paginationEnabled = true;
                        $description = str_replace('<!-- pagebreak -->', '</div><div class="pager-page">', $description);
                        echo '<div class="pager-page">';
                        echo $description;
                        echo '</div>';
                    } else {
                        echo $description;
                    }
                    ?>
                </div>
            </div>
            @if ($paginationEnabled)
            <div class="panel-footer text-center">
                <ul class="pager-controls pagination pagination-sm" style="margin: 0;"></ul>
            </div>
            @endif
        </div>
        @if (count($media) > 0)
            <div class="panel panel-default">
                <div class="panel-heading">Images</div>
                <div class="panel-body" style="padding-top: 0; padding-bottom: 0;">
                    <div class="row">
                        @foreach($media as $m)
                        <div class="col-md-4">
                            <a href="/submissions/{{ $submission->id }}/media/{{ $m->id }}/media" class="fancybox" rel="submission_images">
                                <img src='/submissions/{{ $submission->id }}/media/{{ $m->id }}/media' class='gallery-thumb' />
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="text-center">

        </div>
    </div>
</div>

<hr />

<div id="disqus_thread"></div>
<script>
    var disqus_config = function () {
        @if (Auth::user())
        this.page.remote_auth_s3 = '{{ $disqus_sso_message }} {{ $disqus_sso_hmac }} {{ $disqus_sso_timestamp }}';
        this.page.api_key = '{{ config('services.disqus.public_key') }}';
        @endif
        this.page.url = '{{ route('submission', $submission->id) }}';
        this.page.identifier = 'submission-{{ $submission->id }}';

        this.sso = {
            name:   "GGMaker Account",
            button:  "http://ggmaker.com/wp-content/themes/ggmaker/images/ggmaker-mascot-logo.png",
            icon:    "http://ggmaker.com/wp-content/themes/ggmaker/images/ggmaker-mascot-logo.png",
            url:     "{{ route('auth.login', ['from' => 'disqus']) }}",
            logout:  "{{ route('auth.logout') }}",
            width:   "800",
            height:  "400"
        };
    };
    (function() { // DON'T EDIT BELOW THIS LINE
        var d = document, s = d.createElement('script');

        s.src = '//ggmaker.disqus.com/embed.js';

        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>

@stop

@section('scripts')
    @parent

    <script type="text/javascript">
    $(document).ready(function() {
        $(".pager-container").quickpager({
            step: '1',
            delay: 0,
            pager: $(".pager-controls"),
            page: '{{ $currentPage }}'
        });
    });
    </script>
    <script id="dsq-count-scr" src="//ggmaker.disqus.com/count.js" async></script>

@stop
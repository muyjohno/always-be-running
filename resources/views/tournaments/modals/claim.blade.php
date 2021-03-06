{{--Claim tournament spot modal--}}
<div class="modal fade" id="claimModal" tabindex="-1" role="dialog" aria-labelledby="claim modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Claim spot on tournament<br/>
                    <div class="modal-subtitle" id="modal-subtitle"></div>
                </h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid bd-example-row">
                    {!! Form::open(['url' => "", 'id' => 'create-claim']) !!}
                        <input name="top_number" type="hidden" value="" id="hidden-top-value" />
                        {{--Rank selectors--}}
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    {!! Form::label('rank', 'rank after swiss rounds') !!}
                                    {!! Form::select('rank', [], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6" id="claim-top-section">
                                <div class="form-group">
                                    {!! Form::label('rank_top', 'rank after top cut') !!}
                                    {!! Form::select('rank_top', [], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        {{--Dropdown selectors for decks--}}
                        <div class="row" id="claim-deck-row">
                            <div class="col-xs-12 col-md-6">
                                <div class="deck-loader">loading</div>
                                <div class="form-group">
                                    {!! Form::label('corp_deck', 'corporation deck') !!}
                                    {!! Form::select('corp_deck', [], null, ['class' => 'form-control', 'id' => 'corp_deck']) !!}
                                    <div class="alert alert-danger hidden-xs-up" id="no-corp-deck">
                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                        You don't have any decklist available on NetrunnerDB.
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="deck-loader">loading</div>
                                <div class="form-group">
                                    {!! Form::label('runner_deck', 'runner deck') !!}
                                    {!! Form::select('runner_deck', [], null, ['class' => 'form-control', 'id' => 'runner_deck']) !!}
                                    <div class="alert alert-danger hidden-xs-up" id="no-runner-deck">
                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                        You don't have any decklist available on NetrunnerDB.
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--Login prompt if user is not logged in--}}
                        <div class="text-xs-center hidden-xs-up m-b-1" id="claim-user-login">
                            <div class="alert alert-danger" id="no-runner-deck">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                There was a problem with reaching NetrunnerDB.<br/>
                                Please try logging in again.
                            </div>
                            <a href="/oauth2/redirect">Login via NetrunnerDB</a> to claim spot.
                        </div>
                        {{--claim with other--}}
                        <div class="row">
                            <div class="col-xs-12 text-xs-right">
                                <a data-toggle="collapse" href="#collapse-other-decks" aria-expanded="false" aria-controls="collapse-other-decks">
                                    <em>claim with other</em>
                                </a>
                                @include('partials.popover', ['direction' => 'top', 'content' =>
                            'Use this option if you want to claim with a <strong>published</strong> deck
                            of another user. You can find the deck ID in the URL:<br/>
                            <em>e.g.: netrunnerdb.com/en/decklist/<strong>38734</strong></em>'])
                            </div>
                        </div>
                        {{--Dropdown selectors for decks--}}
                        <div class="row collapse" id="collapse-other-decks">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    {!! Form::label('other_corp_deck', 'corporation deck ID') !!}
                                    {!! Form::text('other_corp_deck', null, ['class' => 'form-control',
                                        'oninput' => "switchDeck('corp_deck', 'other_corp_deck')",
                                        'placeholder' => 'published deck ID']) !!}
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    {!! Form::label('other_runner_deck', 'runner deck ID') !!}
                                    {!! Form::text('other_runner_deck', null, ['class' => 'form-control',
                                        'oninput' => "switchDeck('runner_deck', 'other_runner_deck')",
                                        'placeholder' => 'published deck ID']) !!}
                                </div>
                            </div>
                        </div>
                        {{--Sumbit claim--}}
                        <div class="text-xs-center">
                            <button type="submit" class="btn btn-claim disabled" id="submit-claim" disabled>
                                Claim spot
                            </button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
{{--Script to fill tournament claim modal--}}
<script type="text/javascript">
    var deckData = null, loading = false;

    $('#claimModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var subtitle = button.data('subtitle'), // module subtitle
                id = button.data('tournament-id'),  // tournament ID
                players_number = button.data('players-number'),
                top_number = button.data('top-number');
        var modal = $(this);
        modal.find('.modal-subtitle').text(subtitle);
        modal.find('#create-claim').attr("action", "/tournaments/" + id + "/claim");
        modal.find('#hidden-top-value').val(top_number);

        // ranks
        modal.find('#rank').empty();
        for (var count = 1; count <= players_number; count++) {
            modal.find('#rank').append($('<option>', {value: count, text: count}));
        }

        // top rank
        if (top_number) {
            modal.find('#claim-top-section').removeClass('hidden-xs-up');
            modal.find('#rank_top').empty();
            modal.find('#rank_top').append($('<option>', {value: '0', text: 'below top cut'}));
            for (count = 1; count <= top_number; count++) {
                modal.find('#rank_top').append($('<option>', {value: count, text: count}));
            }
        } else {
            modal.find('#claim-top-section').addClass('hidden-xs-up');
        }

        // load deck via API
        if (!deckData) {
            loadDecks();
        }

        function loadDecks() {
            if (!loading) { // don't start loading multiple times
                modal.find('.deck-loader').addClass('loader').removeClass('hidden-xs-up');
                loading = true;
                $.ajax({
                    url: '/api/userdecks',
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        // hide loader animation
                        modal.find('.deck-loader').removeClass('loader').addClass('hidden-xs-up');

                        // user needs to login
                        if (data.error) {
                            modal.find('#claim-user-login').removeClass('hidden-xs-up');
                            modal.find('#claim-deck-row').addClass('hidden-xs-up');
                            return 0;
                        }

                        displayListOfDecksForClaims('runner', data);
                        displayListOfDecksForClaims('corp', data);

                        // enable submission if there were decks on both sides
                        if (data.privateNetrunnerDB.runner.length + data.publicNetrunnerDB.runner.length > 0 &&
                                data.privateNetrunnerDB.corp.length + data.publicNetrunnerDB.corp.length > 0) {
                            modal.find('#submit-claim').removeClass('disabled').prop("disabled", false);
                        }

                        deckData = data;
                        loading = false;
                    }
                });
            }
        }

        // populates select element for tournament claim form deck selector
        function displayListOfDecksForClaims(side, data) {
            var rootElement = '#'+side+'_deck',
                    publicRoot = modal.find(rootElement),
                    privateRoot = modal.find(rootElement);

            modal.find(rootElement).empty();

            // no deck warning
            if (data.privateNetrunnerDB[side].length + data.publicNetrunnerDB[side].length == 0) {
                modal.find('#no-'+side+'-deck').removeClass('hidden-xs-up');
                modal.find('#'+side+'_deck').addClass('hidden-xs-up');

            } else {

                // add optgroups
                if (data.privateNetrunnerDB[side].length && data.publicNetrunnerDB[side].length) {
                    modal.find(rootElement).append($('<optgroup>', {
                        label: '--- published decks ---',
                        id: side + '_public'
                    }));
                    modal.find(rootElement).append($('<optgroup>', {
                        label: '--- private decks ---',
                        id: side + '_private'
                    }));
                    publicRoot = modal.find('#' + side + '_public');
                    privateRoot = modal.find('#' + side + '_private');
                }

                // add public decks
                if (data.publicNetrunnerDB) {
                    displayDecksForClaims(data.publicNetrunnerDB[side], publicRoot, 1);
                }
                // add private decks
                if (data.privateNetrunnerDB) {
                    displayDecksForClaims(data.privateNetrunnerDB[side], privateRoot, 2);
                }
            }
        }

        // populates option lines for tournament claim form deck selector
        function displayDecksForClaims(data, rootElement, type) {
            // note: ordering by date is note done, relying on NetrunnerDB
            $.each(data, function (index, element) {
                rootElement.append($('<option>', {
                    value: "{ \"title\": \"" + element.name.replace(/'/g, "\\'").replace(/"/g, "\\\\\"") +
                    "\", \"id\": \"" + element.id + "\", \"identity\": \"" + element.identity +
                    "\", \"type\": \"" + type + "\" }",
                    text: element.name
                }));
            });
        }
    });

    // other decks collapse display fix
    $('#collapse-other-decks').on('shown.bs.collapse', function () {
        $('#collapse-other-decks').css({
            'display': 'flex'
        });
    }).on('hidden.bs.collapse', function () {
        $('#collapse-other-decks').css({
            'display': 'none'
        });
    });

    // claim modal: disable own decks if other deck ID is provided
    function switchDeck(idOwn, idOther) {
        if (document.getElementById(idOther).value.length > 0) {
            document.getElementById(idOwn).setAttribute('disabled','');
        } else {
            document.getElementById(idOwn).removeAttribute('disabled');
        }
    }

</script>
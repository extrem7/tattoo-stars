<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContestWork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Http\Resources\ContestWorkResource;
use Modules\Api\Repositories\ContestRepository;

final class ContestController extends Controller
{
    protected ContestRepository $repository;

    public function __construct(ContestRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @api {get} /contest Contest information
     * @apiName ContestIndex
     * @apiGroup Contest
     *
     * @apiUse Token
     * @apiSuccess {Boolean} votedToday Is user voted today.
     * @apiSuccess {Object[]} works Daily works.
     * @apiSuccess {Object[]} yesterdayResults Daily works.
     * @apiSuccess {Object[]} winners All time winners.
     * @apiSuccess {Object} work Work item in works|yesterdayResults|winners collections.
     * @apiSuccess {Number} work.id Work id.
     * @apiSuccess {Date} [work.date] Work date.
     * @apiSuccess {Object} work.post Work post.
     * @apiSuccess {Number} work.post.id Post id.
     * @apiSuccess {Date} work.post.date Post date.
     * @apiSuccess {Object} work.post.user Post user.
     * @apiSuccess {Number} work.post.user.id User id.
     * @apiSuccess {String} work.post.user.name User name.
     * @apiSuccess {String} work.post.user.avatar User avatar.
     * @apiSuccess {String} work.post.thumbnail Post thumbnail.
     * @apiSuccess {String} [work.percents] Work percents.
     * @apiSuccess {String} [work.voteStatus] Is user voted for this work.
     */
    public function index(): JsonResponse
    {
        $totalVotes = $this->repository->getYesterdayVotesCount();

        return response()->json([
            'votedToday' => \Auth::user()->hasVotedToday(),
            'works' => ContestWorkResource::collection($this->repository->getDaily()),
            'yesterdayResults' => ContestWorkResource::collection($this->repository->getYesterdayWinners())->each(
                fn(ContestWorkResource $r) => $r->setTotalVotes($totalVotes)
            ),
            'winners' => ContestWorkResource::collection($this->repository->getWinners())
        ]);
    }

    /**
     * @api {post} /contest/:id/vote Vote for work
     * @apiName ContestVote
     * @apiGroup Contest
     *
     * @apiUse Token
     *
     * @apiSuccess {String} message Vote status message.
     */
    public function vote(ContestWork $work): JsonResponse
    {
        $user = \Auth::user();

        abort_if($user->hasVotedToday(), Response::HTTP_BAD_REQUEST, 'You can only vote once a day.');
        abort_unless($work->date->equalTo(today()), Response::HTTP_FORBIDDEN, 'You can only vote for today\'s work.');

        $work->votes()->save($user);

        return response()->json(['message' => 'You voted for the job.']);
    }
}

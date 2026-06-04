<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Problem, Solution, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Advanced search across problems, solutions, and users
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:all,problems,solutions,users',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = trim($request->input('q'));
        $type = $request->input('type', 'all');
        $limit = min((int) $request->input('limit', 10), 50);

        $results = [];

        if ($type === 'all' || $type === 'problems') {
            $problems = $this->searchProblems($query, $limit);
            $results = array_merge($results, $problems);
        }

        if ($type === 'all' || $type === 'solutions') {
            $solutions = $this->searchSolutions($query, $limit);
            $results = array_merge($results, $solutions);
        }

        if ($type === 'all' || $type === 'users') {
            $users = $this->searchUsers($query, $limit);
            $results = array_merge($results, $users);
        }

        // Sort by relevance (simple implementation based on position in results)
        usort($results, fn($a, $b) => $b['relevance'] <=> $a['relevance']);

        return response()->json([
            'results' => array_slice($results, 0, $limit),
            'total' => count($results),
        ]);
    }

    private function searchProblems(string $query, int $limit): array
    {
        $problems = Problem::query()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($query) {
                      $tagQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->with(['user', 'category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $problems->map(fn($problem) => [
            'type' => 'problem',
            'id' => $problem->id,
            'title' => $problem->title,
            'excerpt' => strip_tags(substr($problem->description, 0, 150)) . '...',
            'url' => route('problems.show', $problem),
            'meta' => "Posted by {$problem->user->name} • {$problem->solutions_count} solutions",
            'relevance' => 100,
        ])->toArray();
    }

    private function searchSolutions(string $query, int $limit): array
    {
        $solutions = Solution::query()
            ->where(function ($q) use ($query) {
                $q->where('content', 'like', "%{$query}%")
                  ->orWhereHas('problem', function ($problemQuery) use ($query) {
                      $problemQuery->where('title', 'like', "%{$query}%");
                  });
            })
            ->with(['user', 'problem'])
            ->orderBy('votes_count', 'desc')
            ->limit($limit)
            ->get();

        return $solutions->map(fn($solution) => [
            'type' => 'solution',
            'id' => $solution->id,
            'title' => "Solution to: {$solution->problem->title}",
            'excerpt' => strip_tags(substr($solution->content, 0, 150)) . '...',
            'url' => route('problems.show', $solution->problem) . '#solution-' . $solution->id,
            'meta' => "By {$solution->user->name} • {$solution->votes_count} votes",
            'relevance' => 90,
        ])->toArray();
    }

    private function searchUsers(string $query, int $limit): array
    {
        $users = User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->orderBy('reputation', 'desc')
            ->limit($limit)
            ->get();

        return $users->map(fn($user) => [
            'type' => 'user',
            'id' => $user->id,
            'title' => $user->name,
            'excerpt' => "@{$user->username}",
            'url' => route('profile.show', $user),
            'meta' => "{$user->reputation} reputation",
            'relevance' => 80,
        ])->toArray();
    }
}
